<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\SalesApiController;
use App\Http\Controllers\Api\CustomersApiController;
use App\Http\Controllers\Api\ExpensesApiController;
use App\Http\Controllers\Api\EmployeesApiController;
use App\Http\Controllers\Api\ReportsApiController;
use App\Http\Controllers\Api\PurchasesApiController;
use App\Http\Controllers\Api\DebtsApiController;
use App\Http\Controllers\Api\ProductsApiController;
use App\Http\Controllers\Api\ShiftApiController;
// use App\Http\Controllers\Api\CatalogApiController; // ← removed (no longer used)

/*
|--------------------------------------------------------------------------
| Public (unauthenticated)
|--------------------------------------------------------------------------
*/

Route::get('/package-info', function () {
    $company = null;

    if (Auth::check()) {
        $company = Auth::user()->company;
    } elseif (Auth::guard('mfanyakazi')->check()) {
        $company = Auth::guard('mfanyakazi')->user()->company;
    }

    if (!$company) {
        return response()->json([
            'success' => false,
            'message' => 'No company found',
        ]);
    }

    $daysLeft = 0;
    if ($company->package_end) {
        $daysLeft = max(0, ceil(now()->diffInDays(
            \Carbon\Carbon::parse($company->package_end),
            false
        )));
    }

    return response()->json([
        'success'      => true,
        'days_left'    => $daysLeft,
        'package_name' => $company->package ?? 'Free Trial',
        'end_date'     => $company->package_end
            ? \Carbon\Carbon::parse($company->package_end)->format('d/m/Y')
            : 'N/A',
    ]);
});

Route::post('/login', [AuthApiController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Authenticated (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // ── Auth ────────────────────────────────────────────────
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me',      [AuthApiController::class, 'me']);

    // ── Shift Yangu (same behaviour as the web shift widget) ─
    Route::prefix('shift')->group(function () {
        Route::get('/current',  [ShiftApiController::class, 'current']);
        Route::post('/start',   [ShiftApiController::class, 'start']);
        Route::post('/close',   [ShiftApiController::class, 'close']);
        Route::get('/sessions', [ShiftApiController::class, 'index']);
        Route::get('/{id}/report', [ShiftApiController::class, 'report'])->whereNumber('id');
        Route::post('/{id}/send-report', [ShiftApiController::class, 'sendReport'])->whereNumber('id');
    });

    // ── Products (bidhaa) ───────────────────────────────────
    Route::prefix('bidhaa')->group(function () {
        Route::get('/',          [ProductsApiController::class, 'index']);
        Route::post('/',         [ProductsApiController::class, 'store']);
        Route::get('/{id}',      [ProductsApiController::class, 'show'])
            ->whereNumber('id');
        Route::put('/{id}',      [ProductsApiController::class, 'update'])
            ->whereNumber('id');
        Route::delete('/{id}',   [ProductsApiController::class, 'destroy'])
            ->whereNumber('id');
        Route::post('/upload-excel', [ProductsApiController::class, 'uploadExcel']);
    });

    // ── Sales (mauzo) ───────────────────────────────────────
    Route::prefix('mauzo')->group(function () {
        Route::get('/',       [SalesApiController::class, 'index']);
        Route::post('/',      [SalesApiController::class, 'store']);
        Route::post('/kopesha', [SalesApiController::class, 'storeLoan']);
    });

    // ── Customers (wateja) ──────────────────────────────────
    Route::prefix('wateja')->group(function () {
        Route::get('/',      [CustomersApiController::class, 'index']);
        Route::get('/{id}',  [CustomersApiController::class, 'show'])
            ->whereNumber('id');
        Route::post('/',     [CustomersApiController::class, 'store']);
        Route::put('/{id}',  [CustomersApiController::class, 'update'])
            ->whereNumber('id');
        Route::delete('/{id}', [CustomersApiController::class, 'destroy'])
            ->whereNumber('id');
    });

    // ── Expenses (matumizi) ─────────────────────────────────
    Route::prefix('matumizi')->group(function () {
        Route::get('/',        [ExpensesApiController::class, 'index']);
        Route::post('/',       [ExpensesApiController::class, 'store']);
        Route::put('/{id}',    [ExpensesApiController::class, 'update'])
            ->whereNumber('id');
        Route::delete('/{id}', [ExpensesApiController::class, 'destroy'])
            ->whereNumber('id');

        Route::get('/aina',         [ExpensesApiController::class, 'listTypes']);
        Route::post('/aina',        [ExpensesApiController::class, 'storeType']);
        Route::delete('/aina/{id}', [ExpensesApiController::class, 'destroyType'])
            ->whereNumber('id');

        Route::get('/report', [ExpensesApiController::class, 'report']);
    });

    // ── Employees (wafanyakazi) ─────────────────────────────
    Route::prefix('wafanyakazi')->group(function () {
        Route::get('/',        [EmployeesApiController::class, 'index']);
        Route::post('/',       [EmployeesApiController::class, 'store']);
        Route::get('/{id}',    [EmployeesApiController::class, 'show'])->whereNumber('id');
        Route::put('/{id}',    [EmployeesApiController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [EmployeesApiController::class, 'destroy'])->whereNumber('id');

        Route::post('/{id}/salary', [EmployeesApiController::class, 'updateSalary'])->whereNumber('id');

        Route::post('/{id}/deductions',          [EmployeesApiController::class, 'addDeduction'])->whereNumber('id');
        Route::post('/deductions/{id}/approve',  [EmployeesApiController::class, 'approveDeduction'])->whereNumber('id');
        Route::post('/deductions/{id}/reject',   [EmployeesApiController::class, 'rejectDeduction'])->whereNumber('id');

        Route::post('/{id}/counter-access', [EmployeesApiController::class, 'toggleCounterAccess'])->whereNumber('id');
    });

    // ── Reports (uchambuzi) ─────────────────────────────────
    Route::prefix('uchambuzi')->group(function () {
        Route::get('/graphs',      [ReportsApiController::class, 'graphs']);
        Route::get('/mwenendo',    [ReportsApiController::class, 'mwenendo']);
        Route::get('/kampuni',     [ReportsApiController::class, 'kampuni']);
        Route::get('/historia',    [ReportsApiController::class, 'historia']);
        Route::get('/wauzaji',     [ReportsApiController::class, 'wauzaji']);
        Route::get('/user-report', [ReportsApiController::class, 'userReport']);
    });

    // ── Purchases (manunuzi) ────────────────────────────────
    Route::prefix('manunuzi')->group(function () {
        Route::get('/',        [PurchasesApiController::class, 'index']);
        Route::post('/',       [PurchasesApiController::class, 'store']);
        Route::get('/{id}',    [PurchasesApiController::class, 'show'])->whereNumber('id');
        Route::put('/{id}',    [PurchasesApiController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [PurchasesApiController::class, 'destroy'])->whereNumber('id');

        Route::get('/drafts',                [PurchasesApiController::class, 'listDrafts']);
        Route::post('/drafts',               [PurchasesApiController::class, 'saveDraft']);
        Route::get('/drafts/{token}',        [PurchasesApiController::class, 'loadDraft']);
        Route::delete('/drafts/{token}',     [PurchasesApiController::class, 'deleteDraft']);
        Route::post('/drafts/{token}/finalize', [PurchasesApiController::class, 'finalizeDraft']);
    });

    // ── Debts (madeni) ──────────────────────────────────────
    Route::prefix('madeni')->group(function () {
        Route::get('/',               [DebtsApiController::class, 'index']);
        Route::get('/repayments',     [DebtsApiController::class, 'repayments']);
        Route::get('/borrowers/{name}/debts', [DebtsApiController::class, 'borrowerDebts']);
        Route::get('/{id}',           [DebtsApiController::class, 'show'])->whereNumber('id');
        Route::post('/{id}/repay',    [DebtsApiController::class, 'repay'])->whereNumber('id');
    });
});