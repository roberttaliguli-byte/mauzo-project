<?php
// app/Http/Controllers/WafanyakaziController.php

namespace App\Http\Controllers;

use App\Models\Wafanyakazi;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalaryDeduction;
use App\Models\Order;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class WafanyakaziController extends Controller
{
    /**
     * Get the authenticated user from either guard
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
     * Check if user is Boss/Admin
     */
    private function isBoss()
    {
        return Auth::guard('web')->check();
    }
    
    /**
     * Get company ID for current user
     */
    private function getCompanyId()
    {
        $user = $this->getAuthenticatedUser();
        return $user->company_id;
    }

    /**
     * Get company name
     */
    private function getCompanyName()
    {
        $companyId = $this->getCompanyId();
        $company = Company::find($companyId);
        return $company ? $company->company_name : 'Kampuni';
    }

    /**
     * Get current user ID
     */
    private function getUserId()
    {
        $user = $this->getAuthenticatedUser();
        return $user->id;
    }

    /**
     * Display a listing of employees
     */
    public function index(Request $request)
    {
        try {
            $companyId = $this->getCompanyId();
            $perPage = $request->input('per_page', 10);

            $query = Wafanyakazi::where('company_id', $companyId);

            // Search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('jina', 'LIKE', "%{$search}%")
                      ->orWhere('simu', 'LIKE', "%{$search}%")
                      ->orWhere('barua_pepe', 'LIKE', "%{$search}%")
                      ->orWhere('username', 'LIKE', "%{$search}%")
                      ->orWhere('remarks', 'LIKE', "%{$search}%");
                });
            }

            // Status filter
            if ($request->has('status') && !empty($request->status)) {
                if ($request->status === 'active') {
                    $query->where('getini', 'ingia');
                } elseif ($request->status === 'inactive') {
                    $query->where('getini', 'simama');
                }
            }

            // Uwezo filter
            if ($request->has('uwezo') && !empty($request->uwezo)) {
                $query->where('uwezo', $request->uwezo);
            }

            // Counter access filter
            if ($request->has('counter') && $request->counter === 'true') {
                $query->where('allow_counter_access', true);
            }

            $wafanyakazi = $query->latest()
                                ->paginate($perPage)
                                ->appends($request->except('page'));

            // Statistics
            $totalEmployees = Wafanyakazi::where('company_id', $companyId)->count();
            $activeEmployees = Wafanyakazi::where('company_id', $companyId)->where('getini', 'ingia')->count();
            $maleEmployees = Wafanyakazi::where('company_id', $companyId)->where('jinsia', 'Mwanaume')->count();
            $femaleEmployees = Wafanyakazi::where('company_id', $companyId)->where('jinsia', 'Mwanamke')->count();

            // Salary stats
            $totalSalary = Wafanyakazi::where('company_id', $companyId)->sum('salary') ?? 0;
            
            // Check if employee_salary_deductions table exists
            $totalDeductions = 0;
            if (Schema::hasTable('employee_salary_deductions')) {
                $totalDeductions = EmployeeSalaryDeduction::where('company_id', $companyId)
                    ->where('status', 'approved')
                    ->sum('amount') ?? 0;
            }
            
            $totalCounterAccess = Wafanyakazi::where('company_id', $companyId)
                ->where('allow_counter_access', true)
                ->count();

            // Get employees with counter access for quick view
            $counterEmployees = Wafanyakazi::where('company_id', $companyId)
                ->where('allow_counter_access', true)
                ->where('getini', 'ingia')
                ->get(['id', 'jina', 'simu']);

            // Export PDF
            if ($request->has('export') && $request->export === 'pdf') {
                $data = [
                    'wafanyakazi' => Wafanyakazi::where('company_id', $companyId)->latest()->get(),
                    'title' => 'Orodha ya Wafanyakazi',
                    'date' => now()->format('d/m/Y'),
                    'company' => $this->getCompanyName(),
                    'total_employees' => $totalEmployees,
                    'active_employees' => $activeEmployees,
                    'male_employees' => $maleEmployees,
                    'female_employees' => $femaleEmployees,
                ];
                
                $pdf = Pdf::loadView('wafanyakazi.pdf', $data);
                return $pdf->download('wafanyakazi-' . date('Y-m-d') . '.pdf');
            }

            return view('wafanyakazi.index', compact(
                'wafanyakazi', 
                'totalEmployees', 
                'activeEmployees', 
                'maleEmployees', 
                'femaleEmployees',
                'totalSalary',
                'totalDeductions',
                'totalCounterAccess',
                'counterEmployees'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading employees: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hitilafu katika kupakia wafanyakazi: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created employee
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'jina' => 'required|string|max:255',
                'simu' => 'nullable|string|max:20|unique:wafanyakazis,simu',
                'jinsia' => 'required|string|in:Mwanaume,Mwanamke',
                'anuani' => 'nullable|string|max:500',
                'barua_pepe' => 'nullable|email|max:255|unique:wafanyakazis,barua_pepe',
                'ndugu' => 'nullable|string|max:255',
                'simu_ndugu' => 'nullable|string|max:20',
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:users,username',
                    'unique:wafanyakazis,username',
                    'regex:/^[a-zA-Z0-9_]+$/'
                ],
                'password' => 'required|string|min:6|max:255',
                'tarehe_kuzaliwa' => 'nullable|date|before:today',
                'uwezo' => 'nullable|in:mdogo,mkubwa',
                'salary' => 'nullable|numeric|min:0',
                'allow_counter_access' => 'nullable|boolean',
                'remarks' => 'nullable|string|max:500',
                'getini' => 'nullable|in:simama,ingia'
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors(),
                        'message' => $validator->errors()->first()
                    ], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = [
                'jina' => $request->jina,
                'simu' => $request->simu,
                'jinsia' => $request->jinsia,
                'anuani' => $request->anuani,
                'barua_pepe' => $request->barua_pepe,
                'ndugu' => $request->ndugu,
                'simu_ndugu' => $request->simu_ndugu,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'tarehe_kuzaliwa' => $request->tarehe_kuzaliwa,          
                'company_id' => $this->getCompanyId(),
                'role' => 'mfanyakazi',
                'getini' => $request->getini ?? 'simama',
                'uwezo' => $request->uwezo ?? 'mdogo',
                'salary' => $request->salary ?? 0,
                'allow_counter_access' => $request->has('allow_counter_access') && $request->allow_counter_access == 1,
                'remarks' => $request->remarks,
                'active' => true
            ];

            $employee = Wafanyakazi::create($data);

            Log::info('Employee created', [
                'employee_id' => $employee->id,
                'company_id' => $this->getCompanyId(),
                'created_by' => $this->getUserId()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mfanyakazi amesajiliwa kikamilifu!',
                    'data' => $employee
                ]);
            }

            return redirect()->route('wafanyakazi.index')
                ->with('success', 'Mfanyakazi amesajiliwa kikamilifu!');

        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hitilafu katika kusajili mfanyakazi: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Hitilafu katika kusajili mfanyakazi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified employee
     */
    public function update(Request $request, $id)
    {
        try {
            $companyId = $this->getCompanyId();
            $mfanyakazi = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'jina' => 'required|string|max:255',
                'simu' => 'nullable|string|max:20|unique:wafanyakazis,simu,' . $id,
                'jinsia' => 'required|string|in:Mwanaume,Mwanamke',
                'anuani' => 'nullable|string|max:500',
                'barua_pepe' => 'nullable|email|max:255|unique:wafanyakazis,barua_pepe,' . $id,
                'ndugu' => 'nullable|string|max:255',
                'simu_ndugu' => 'nullable|string|max:20',
                'username' => 'nullable|string|max:255|unique:wafanyakazis,username,' . $id,
                'password' => 'nullable|string|min:6|max:255',
                'tarehe_kuzaliwa' => 'nullable|date|before:today',
                'getini' => 'nullable|in:simama,ingia',
                'uwezo' => 'nullable|in:mdogo,mkubwa',
                'salary' => 'nullable|numeric|min:0',
                'allow_counter_access' => 'nullable|boolean',
                'remarks' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors(),
                        'message' => $validator->errors()->first()
                    ], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->except(['password', '_token', '_method']);

            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            } else {
                unset($data['password']);
            }

            $data['allow_counter_access'] = $request->has('allow_counter_access') && $request->allow_counter_access == 1;

            if ($request->has('getini') && $request->getini === 'ingia') {
                $data['active'] = true;
            } elseif ($request->has('getini') && $request->getini === 'simama') {
                $data['active'] = false;
            }

            $mfanyakazi->update($data);

            Log::info('Employee updated', [
                'employee_id' => $mfanyakazi->id,
                'company_id' => $this->getCompanyId(),
                'updated_by' => $this->getUserId()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Taarifa za mfanyakazi zimesasishwa!',
                    'data' => $mfanyakazi
                ]);
            }

            return redirect()->route('wafanyakazi.index')
                ->with('success', 'Taarifa za mfanyakazi zimesasishwa!');

        } catch (\Exception $e) {
            Log::error('Error updating employee: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hitilafu katika kusasisha mfanyakazi: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Hitilafu katika kusasisha mfanyakazi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified employee
     */
    public function destroy($id, Request $request)
    {
        try {
            $companyId = $this->getCompanyId();
            $mfanyakazi = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);
            
            $orderCount = Order::where('created_by', $id)->count();
            
            if ($orderCount > 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Haiwezi kufuta mfanyakazi huyu kwa sababu ana ' . $orderCount . ' orders zilizorekodiwa.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Haiwezi kufuta mfanyakazi huyu kwa sababu ana ' . $orderCount . ' orders zilizorekodiwa.');
            }

            $employeeName = $mfanyakazi->jina;
            $mfanyakazi->delete();

            Log::info('Employee deleted', [
                'employee_id' => $id,
                'employee_name' => $employeeName,
                'company_id' => $this->getCompanyId(),
                'deleted_by' => $this->getUserId()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mfanyakazi amefutwa kikamilifu!'
                ]);
            }

            return redirect()->route('wafanyakazi.index')
                ->with('success', 'Mfanyakazi amefutwa kikamilifu!');

        } catch (\Exception $e) {
            Log::error('Error deleting employee: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hitilafu katika kufuta mfanyakazi: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Hitilafu katika kufuta mfanyakazi: ' . $e->getMessage());
        }
    }

    /**
     * Get employee salary details
     */
    public function getSalaryDetails($id)
    {
        try {
            $companyId = $this->getCompanyId();
            $employee = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);
            
            $salaries = EmployeeSalary::where('company_id', $companyId)
                ->where('mfanyakazi_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            $deductions = EmployeeSalaryDeduction::where('company_id', $companyId)
                ->where('mfanyakazi_id', $id)
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->get();
                
            $pendingDeductions = EmployeeSalaryDeduction::where('company_id', $companyId)
                ->where('mfanyakazi_id', $id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $totalDeductions = $deductions->sum('amount');
            $pendingTotal = $pendingDeductions->sum('amount');
                
            return response()->json([
                'success' => true,
                'data' => [
                    'employee' => $employee,
                    'salaries' => $salaries,
                    'deductions' => $deductions,
                    'pending_deductions' => $pendingDeductions,
                    'current_salary' => $employee->salary ?? 0,
                    'total_deductions' => $totalDeductions,
                    'pending_deductions_total' => $pendingTotal,
                    'net_salary' => ($employee->salary ?? 0) - $totalDeductions,
                    'has_pending_deductions' => $pendingDeductions->count() > 0
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting salary details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kupata taarifa za mshahara: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update employee salary
     */
    public function updateSalary(Request $request, $id)
    {
        try {
            $companyId = $this->getCompanyId();
            $employee = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'salary' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:10',
                'frequency' => 'nullable|string|in:monthly,weekly,daily',
                'remarks' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => $validator->errors()->first()
                ], 422);
            }
            
            DB::beginTransaction();
            
            $employee->salary = $request->salary;
            $employee->save();
            
            EmployeeSalary::create([
                'company_id' => $companyId,
                'mfanyakazi_id' => $id,
                'salary_amount' => $request->salary,
                'currency' => $request->currency ?? 'TZS',
                'frequency' => $request->frequency ?? 'monthly',
                'effective_date' => now(),
                'remarks' => $request->remarks,
                'created_by' => $this->getUserId()
            ]);

            DB::commit();
            
            Log::info('Salary updated', [
                'employee_id' => $id,
                'new_salary' => $request->salary,
                'updated_by' => $this->getUserId()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Mshahara umesasishwa kikamilifu!',
                'data' => [
                    'employee' => $employee,
                    'salary' => $request->salary
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating salary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kusasisha mshahara: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add salary deduction
     */
    public function addDeduction(Request $request, $id)
    {
        try {
            $companyId = $this->getCompanyId();
            $employee = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0',
                'reason' => 'required|string|max:500',
                'order_id' => 'nullable|exists:orders,id',
                'remarks' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $totalDeductions = EmployeeSalaryDeduction::where('company_id', $companyId)
                ->where('mfanyakazi_id', $id)
                ->where('status', 'approved')
                ->sum('amount');
                
            if (($totalDeductions + $request->amount) > $employee->salary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kato hii inazidi mshahara wa mfanyakazi. Mshahara: ' . number_format($employee->salary, 2) . ', Makato yaliyopo: ' . number_format($totalDeductions, 2)
                ], 422);
            }
            
            $deduction = EmployeeSalaryDeduction::create([
                'company_id' => $companyId,
                'mfanyakazi_id' => $id,
                'order_id' => $request->order_id,
                'amount' => $request->amount,
                'reason' => $request->reason,
                'status' => 'pending',
                'remarks' => $request->remarks,
                'approved_by' => null,
                'approved_at' => null
            ]);

            Log::info('Deduction added', [
                'employee_id' => $id,
                'amount' => $request->amount,
                'reason' => $request->reason,
                'added_by' => $this->getUserId()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Kato imeongezwa! Inasubiri idhini.',
                'data' => $deduction
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding deduction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kuongeza kato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve salary deduction
     */
    public function approveDeduction(Request $request, $id)
    {
        try {
            $companyId = $this->getCompanyId();
            $deduction = EmployeeSalaryDeduction::where('company_id', $companyId)->findOrFail($id);
            
            if ($deduction->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kato hii tayari imeidhinishwa.'
                ], 422);
            }

            $employee = Wafanyakazi::find($deduction->mfanyakazi_id);
            $totalDeductions = EmployeeSalaryDeduction::where('company_id', $companyId)
                ->where('mfanyakazi_id', $deduction->mfanyakazi_id)
                ->where('status', 'approved')
                ->sum('amount');
                
            if (($totalDeductions + $deduction->amount) > $employee->salary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kato hii inazidi mshahara wa mfanyakazi. Mshahara: ' . number_format($employee->salary, 2) . ', Makato yaliyopo: ' . number_format($totalDeductions, 2)
                ], 422);
            }
            
            $deduction->status = 'approved';
            $deduction->approved_by = $this->getUserId();
            $deduction->approved_at = now();
            $deduction->save();

            Log::info('Deduction approved', [
                'deduction_id' => $id,
                'employee_id' => $deduction->mfanyakazi_id,
                'amount' => $deduction->amount,
                'approved_by' => $this->getUserId()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Kato imeidhinishwa!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving deduction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kuidhinisha kato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject salary deduction
     */
    public function rejectDeduction($id)
    {
        try {
            $companyId = $this->getCompanyId();
            $deduction = EmployeeSalaryDeduction::where('company_id', $companyId)->findOrFail($id);
            
            if ($deduction->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kato hii tayari imeidhinishwa, haiwezi kukataliwa.'
                ], 422);
            }
            
            $deduction->status = 'rejected';
            $deduction->save();

            Log::info('Deduction rejected', [
                'deduction_id' => $id,
                'employee_id' => $deduction->mfanyakazi_id,
                'amount' => $deduction->amount,
                'rejected_by' => $this->getUserId()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Kato imekataliwa!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting deduction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kukataa kato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle employee counter access
     */
    public function toggleCounterAccess($id)
    {
        try {
            $companyId = $this->getCompanyId();
            $employee = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);
            
            $employee->allow_counter_access = !$employee->allow_counter_access;
            $employee->save();

            Log::info('Counter access toggled', [
                'employee_id' => $id,
                'new_status' => $employee->allow_counter_access,
                'toggled_by' => $this->getUserId()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Ruhusa ya Counter imebadilishwa!',
                'data' => [
                    'allow_counter_access' => $employee->allow_counter_access
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling counter access: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kubadilisha ruhusa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee performance summary
     */
    public function getPerformanceSummary($id)
    {
        try {
            $companyId = $this->getCompanyId();
            $employee = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);
            
            $totalOrders = Order::where('company_id', $companyId)
                ->where('created_by', $id)
                ->count();
                
            $paidOrders = Order::where('company_id', $companyId)
                ->where('created_by', $id)
                ->where('status', 'paid')
                ->count();
                
            $pendingOrders = Order::where('company_id', $companyId)
                ->where('created_by', $id)
                ->whereIn('status', ['saved', 'confirmed'])
                ->count();
                
            $totalRevenue = Order::where('company_id', $companyId)
                ->where('created_by', $id)
                ->where('status', 'paid')
                ->sum('total') ?? 0;
                
            $cancelledOrders = Order::where('company_id', $companyId)
                ->where('created_by', $id)
                ->where('status', 'cancelled')
                ->count();
                
            $totalDeductions = EmployeeSalaryDeduction::where('company_id', $companyId)
                ->where('mfanyakazi_id', $id)
                ->where('status', 'approved')
                ->sum('amount') ?? 0;
            
            $completionRate = $totalOrders > 0 ? round(($paidOrders / $totalOrders) * 100, 2) : 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'employee' => $employee,
                    'total_orders' => $totalOrders,
                    'paid_orders' => $paidOrders,
                    'pending_orders' => $pendingOrders,
                    'cancelled_orders' => $cancelledOrders,
                    'total_revenue' => $totalRevenue,
                    'total_deductions' => $totalDeductions,
                    'current_salary' => $employee->salary ?? 0,
                    'net_salary' => ($employee->salary ?? 0) - $totalDeductions,
                    'completion_rate' => $completionRate,
                    'performance_rating' => $this->getPerformanceRating($completionRate)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting performance summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kupata muhtasari wa utendaji: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance rating based on completion rate
     */
    private function getPerformanceRating($rate)
    {
        if ($rate >= 90) return ['label' => 'Bora', 'class' => 'bg-green-100 text-green-800'];
        if ($rate >= 75) return ['label' => 'Nzuri', 'class' => 'bg-blue-100 text-blue-800'];
        if ($rate >= 50) return ['label' => 'Wastani', 'class' => 'bg-yellow-100 text-yellow-800'];
        return ['label' => 'Inahitaji Kuboresha', 'class' => 'bg-red-100 text-red-800'];
    }

    /**
     * Calculate employee loss from orders
     */
    private function calculateEmployeeLoss($employeeId, $companyId)
    {
        $cancelledLoss = Order::where('company_id', $companyId)
            ->where('created_by', $employeeId)
            ->where('status', 'cancelled')
            ->sum('total') ?? 0;
            
        $overdueLoss = Order::where('company_id', $companyId)
            ->where('created_by', $employeeId)
            ->whereIn('status', ['saved', 'confirmed'])
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->sum('total') ?? 0;
            
        return $cancelledLoss + $overdueLoss;
    }

    /**
     * Get employee order summary for salary page
     */
    public function getEmployeeOrderSummary($id)
    {
        try {
            $companyId = $this->getCompanyId();
            $employee = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);
            
            $orders = Order::where('company_id', $companyId)
                ->where('created_by', $id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $unpaidOrders = $orders->whereIn('status', ['saved', 'confirmed'])->values();
            $cancelledOrders = $orders->where('status', 'cancelled')->values();
            $paidOrders = $orders->where('status', 'paid')->values();
            
            $totalLoss = $this->calculateEmployeeLoss($id, $companyId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'employee' => $employee,
                    'unpaid_orders' => $unpaidOrders,
                    'cancelled_orders' => $cancelledOrders,
                    'paid_orders' => $paidOrders,
                    'total_unpaid' => $unpaidOrders->sum('total'),
                    'total_cancelled' => $cancelledOrders->sum('total'),
                    'total_loss' => $totalLoss,
                    'total_orders' => $orders->count(),
                    'total_paid' => $paidOrders->sum('total'),
                    'completion_rate' => $orders->count() > 0 ? round(($paidOrders->count() / $orders->count()) * 100, 2) : 0
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting employee order summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kupata muhtasari wa orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add deduction from order
     */
    public function addDeductionFromOrder(Request $request)
    {
        try {
            $companyId = $this->getCompanyId();
            
            $validator = Validator::make($request->all(), [
                'mfanyakazi_id' => 'required|exists:wafanyakazis,id',
                'order_id' => 'required|exists:orders,id',
                'amount' => 'required|numeric|min:0',
                'reason' => 'required|string|max:500',
                'remarks' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $existing = EmployeeSalaryDeduction::where('company_id', $companyId)
                ->where('order_id', $request->order_id)
                ->where('mfanyakazi_id', $request->mfanyakazi_id)
                ->first();
                
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kato tayari imeongezwa kwa order hii'
                ], 422);
            }

            $employee = Wafanyakazi::find($request->mfanyakazi_id);
            $totalDeductions = EmployeeSalaryDeduction::where('company_id', $companyId)
                ->where('mfanyakazi_id', $request->mfanyakazi_id)
                ->where('status', 'approved')
                ->sum('amount');
                
            if (($totalDeductions + $request->amount) > $employee->salary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kato hii inazidi mshahara wa mfanyakazi. Mshahara: ' . number_format($employee->salary, 2)
                ], 422);
            }

            $deduction = EmployeeSalaryDeduction::create([
                'company_id' => $companyId,
                'mfanyakazi_id' => $request->mfanyakazi_id,
                'order_id' => $request->order_id,
                'amount' => $request->amount,
                'reason' => $request->reason,
                'status' => 'pending',
                'remarks' => $request->remarks,
                'approved_by' => null,
                'approved_at' => null
            ]);

            Log::info('Deduction added from order', [
                'employee_id' => $request->mfanyakazi_id,
                'order_id' => $request->order_id,
                'amount' => $request->amount,
                'added_by' => $this->getUserId()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Kato imeongezwa! Inasubiri idhini.',
                'data' => $deduction
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding deduction from order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kuongeza kato: ' . $e->getMessage()
            ], 500);
        }
    }
}