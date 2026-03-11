<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MtejaController;
use App\Http\Controllers\MatumiziController;
use App\Http\Controllers\WafanyakaziController;
use App\Http\Controllers\MauzoController;
use App\Http\Controllers\MasaplayaController;
use App\Http\Controllers\BidhaaController;
use App\Http\Controllers\ManunuziController;
use App\Http\Controllers\MadeniController;
use App\Http\Controllers\UchambuziController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController as MainReportController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\AdminCompanyActivityController;
use App\Http\Controllers\Admin\CompanyActivityController;
use App\Models\Bidhaa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// =========================
// Public routes
// =========================
Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'registerPost'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');

Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');

// Forgot password – email input
Route::get('/forgot-password', [AuthController::class, 'showEmailForm'])
    ->middleware('guest')
    ->name('password.request');

// Send reset link to email
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->middleware('guest')
    ->name('password.email');

// Show reset password form (from email link)
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

// Update password
Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('password.update');

// =========================
// Routes accessible by both guards (boss and employee)
// =========================
Route::middleware(['auth.any'])->group(function () {
    
    // Auth & Profile routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/password/change', [PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/password/update', [PasswordController::class, 'update'])->name('password.update.auth');
    Route::get('/company/info', [ProfileController::class, 'companyInfo'])->name('company.info');
    Route::put('/company/update', [CompanyController::class, 'update'])->name('company.update');
    Route::get('/company/check-email', [CompanyController::class, 'checkEmail'])->name('company.check.email');
    
    // Session routes
    Route::get('/check-session', [AuthController::class, 'checkSession'])->name('check.session');
    Route::post('/cleanup-session', [AuthController::class, 'cleanupSession'])->name('cleanup.session');
    
    // =========================
    // API Routes (accessible by both guards)
    // =========================
    
    // Low Stock API Route
    Route::get('/api/low-stock-products', function(Request $request) {
        try {
            // Get user from either guard
            $user = null;
            if (Auth::check()) {
                $user = Auth::user();
            } elseif (Auth::guard('mfanyakazi')->check()) {
                $user = Auth::guard('mfanyakazi')->user();
            }
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'count' => 0,
                    'products' => []
                ], 401);
            }
            
            // Get company ID from user
            $companyId = $user->company_id;
            
            // If no company ID, return empty response
            if (!$companyId) {
                return response()->json([
                    'success' => true,
                    'count' => 0,
                    'products' => []
                ]);
            }
            
            // Fetch low stock products (idadi <= 9)
            $products = Bidhaa::where('company_id', $companyId)
                ->where('idadi', '<=', 9)
                ->where('idadi', '>', 0) // Exclude zero stock
                ->orderBy('idadi', 'asc') // Lowest stock first
                ->get(['id', 'jina', 'aina', 'kipimo', 'idadi', 'bei_kuuza', 'barcode']);
            
            return response()->json([
                'success' => true,
                'count' => $products->count(),
                'products' => $products
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching low stock products',
                'count' => 0,
                'products' => []
            ], 500);
        }
    })->name('api.low-stock-products');
    
    // Package info API route
    Route::get('/api/package-info', function() {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        } elseif (Auth::guard('mfanyakazi')->check()) {
            $user = Auth::guard('mfanyakazi')->user();
        }
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $company = $user->company;
        
        $daysLeft = 0;
        $packageName = 'Free Trial';
        $endDate = 'N/A';
        
        if ($company) {
            $packageName = $company->package ?? 'Free Trial';
            
            if ($company->package_end) {
                $endDate = \Carbon\Carbon::parse($company->package_end)->format('d/m/Y');
                $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($company->package_end), false);
                $daysLeft = max(0, ceil($daysLeft));
            }
        }
        
        return response()->json([
            'success' => true,
            'days_left' => $daysLeft,
            'package_name' => $packageName,
            'end_date' => $endDate
        ]);
    })->name('api.package.info');
    
    // Send package email notification API
    Route::post('/api/send-package-email', function(Request $request) {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        } elseif (Auth::guard('mfanyakazi')->check()) {
            $user = Auth::guard('mfanyakazi')->user();
        }
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $company = $user->company;
        
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 404);
        }
        
        if (!$company->email) {
            return response()->json(['success' => false, 'message' => 'No email address found'], 400);
        }
        
        $daysLeft = $request->days_left;
        $packageName = $request->package_name;
        
        try {
            Mail::to($company->email)->send(
                new \App\Mail\PackageExpiryNotification($company, $daysLeft, $packageName)
            );
            
            // Update last notification time
            $company->last_package_notification_sent = now();
            $company->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Email notification sent successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send package email: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    })->name('api.send.package.email');
    
    // ================================
    // Wateja Routes (accessible by both)
    // ================================
    Route::get('/wateja', [MtejaController::class, 'index'])->name('wateja.index');
    Route::post('/wateja', [MtejaController::class, 'store'])->name('wateja.store');
    Route::put('/wateja/{mteja}', [MtejaController::class, 'update'])->name('wateja.update');
    Route::delete('/wateja/{mteja}', [MtejaController::class, 'destroy'])->name('wateja.destroy');
    
    // ================================
    // Matumizi Routes (accessible by both)
    // ================================
    Route::resource('matumizi', MatumiziController::class)->except(['show']);
    Route::post('/matumizi/sajili-aina', [MatumiziController::class, 'sajiliAina'])->name('matumizi.sajili-aina');
    Route::delete('/matumizi/aina/{id}', [MatumiziController::class, 'destroyAina'])->name('matumizi.destroy-aina');
    Route::get('/matumizi/export-pdf', [MatumiziController::class, 'exportPDF'])->name('matumizi.export.pdf');
    Route::get('/matumizi/export-report-pdf', [MatumiziController::class, 'exportReportPDF'])->name('matumizi.export.report.pdf');
    
    // ================================
    // Mauzo Routes (accessible by both)
    // ================================
    Route::get('/mauzo', [MauzoController::class, 'index'])->name('mauzo.index');
    Route::post('/mauzo', [MauzoController::class, 'store'])->name('mauzo.store');
    Route::delete('/mauzo/{id}', [MauzoController::class, 'destroy'])->name('mauzo.destroy');
    Route::post('/mauzo/barcode', [MauzoController::class, 'storeBarcode'])->name('mauzo.store.barcode');
    Route::post('/mauzo/kikapu', [MauzoController::class, 'storeKikapu'])->name('mauzo.store.kikapu');
    Route::post('/mauzo/kikapu/loan', [MauzoController::class, 'storeKikapuLoan'])->name('mauzo.store.kikapu.loan');
    Route::post('/mauzo/kopesha', [MauzoController::class, 'storeKikapuLoan'])->name('mauzo.store.kopesha');
    Route::post('/mauzo/kopesha/barcode', [MauzoController::class, 'storeKikapuLoan'])->name('mauzo.store.kopesha.barcode');
    Route::get('/mauzo/check-double-sale/{bidhaaId}', [MauzoController::class, 'checkDoubleSale'])->name('mauzo.check.double.sale');
    Route::get('/mauzo/receipt-data/{receiptNo}', [MauzoController::class, 'getReceiptData'])->name('mauzo.receipt.data');
    Route::post('/mauzo/filtered-sales', [MauzoController::class, 'getFilteredSales'])->name('mauzo.filtered');
    Route::get('/mauzo/search-receipts', [MauzoController::class, 'searchReceipts'])->name('mauzo.search.receipts');
    Route::get('/mauzo/receipt-print/{receiptNo}', [MauzoController::class, 'getReceiptForPrint'])->name('mauzo.receipt.print');
    Route::get('/mauzo/thermal-receipt/{receiptNo}', [MauzoController::class, 'printThermalReceipt'])->name('mauzo.thermal.receipt');
    Route::get('/mauzo/financial-data', [MauzoController::class, 'getFinancialData'])->name('mauzo.financial.data');
    Route::get('/mauzo/product-by-barcode/{barcode}', [MauzoController::class, 'getProductByBarcode'])->name('mauzo.product.by.barcode');
    Route::post('/mauzo/update-stock', [MauzoController::class, 'updateStock'])->name('mauzo.update.stock');
    
    // ================================
    // Bidhaa Routes (accessible by both)
    // ================================
    Route::get('/bidhaa', [BidhaaController::class, 'index'])->name('bidhaa.index');
    Route::post('/bidhaa', [BidhaaController::class, 'store'])->name('bidhaa.store');
    Route::put('/bidhaa/{id}', [BidhaaController::class, 'update'])->name('bidhaa.update');
    Route::delete('/bidhaa/{id}', [BidhaaController::class, 'destroy'])->name('bidhaa.destroy');
    Route::get('/bidhaa/search', [BidhaaController::class, 'searchAll'])->name('bidhaa.search');
    Route::get('/bidhaa/{id}/edit-product', [BidhaaController::class, 'editProduct'])->name('bidhaa.edit-product');
    Route::post('/bidhaa/upload-excel', [BidhaaController::class, 'uploadExcel'])->name('bidhaa.uploadExcel');
    Route::get('/bidhaa/download-sample', [BidhaaController::class, 'downloadSample'])->name('bidhaa.downloadSample');
    Route::post('/bidhaa/barcode', [BidhaaController::class, 'storeBarcode'])->name('bidhaa.store.barcode');
    Route::get('/bidhaa/tafuta-barcode/{barcode}', [BidhaaController::class, 'tafutaBarcode'])->name('bidhaa.tafuta.barcode');
    Route::get('/bidhaa/taarifa', [BidhaaController::class, 'taarifa'])->name('bidhaa.taarifa');
    Route::get('/bidhaa/export-details/{id}', [BidhaaController::class, 'exportProductDetails'])->name('bidhaa.export-details');
    Route::get('/bidhaa/search-products', [BidhaaController::class, 'searchProducts'])->name('bidhaa.search-products');
    
    // ================================
    // Madeni Routes (accessible by both)
    // ================================
    Route::prefix('madeni')->group(function () {
        Route::get('/', [MadeniController::class, 'index'])->name('madeni.index');
        Route::post('/', [MadeniController::class, 'store'])->name('madeni.store');
        Route::get('/search', [MadeniController::class, 'search'])->name('madeni.search');
        Route::get('/borrower-debts', [MadeniController::class, 'borrowerDebts'])->name('madeni.borrower.debts');
        Route::post('/{madeni}/rejesha', [MadeniController::class, 'rejesha'])->name('madeni.rejesha');
        Route::put('/{madeni}', [MadeniController::class, 'update'])->name('madeni.update');
        Route::delete('/{madeni}', [MadeniController::class, 'destroy'])->name('madeni.destroy');
        Route::get('/export', [MadeniController::class, 'export'])->name('madeni.export');
        Route::get('/report/pdf', [MadeniController::class, 'reportPdf'])->name('madeni.report.pdf');
        Route::get('/report/excel', [MadeniController::class, 'reportExcel'])->name('madeni.report.excel');
    });
});

// =========================
// Boss only routes
// =========================
Route::middleware(['auth', 'role:boss'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/uchambuzi', [UchambuziController::class, 'index'])->name('uchambuzi.index');
    Route::get('/uchambuzi/mwenendo', [UchambuziController::class, 'mwenendoRange'])->name('uchambuzi.mwenendo.range');
    
    // Manunuzi routes (boss only)
    Route::get('/manunuzi', [ManunuziController::class, 'index'])->name('manunuzi.index');
    Route::post('/manunuzi', [ManunuziController::class, 'store'])->name('manunuzi.store');
    Route::put('/manunuzi/{manunuzi}', [ManunuziController::class, 'update'])->name('manunuzi.update');
    Route::delete('/manunuzi/{manunuzi}', [ManunuziController::class, 'destroy'])->name('manunuzi.destroy');
    
    // Wafanyakazi routes (boss only)
    Route::get('/wafanyakazi', [WafanyakaziController::class, 'index'])->name('wafanyakazi.index');
    Route::post('/wafanyakazi', [WafanyakaziController::class, 'store'])->name('wafanyakazi.store');
    Route::get('/wafanyakazi/{id}/edit', [WafanyakaziController::class, 'edit'])->name('wafanyakazi.edit');
    Route::put('/wafanyakazi/{id}', [WafanyakaziController::class, 'update'])->name('wafanyakazi.update');
    Route::delete('/wafanyakazi/{id}', [WafanyakaziController::class, 'destroy'])->name('wafanyakazi.destroy');
    Route::get('/wafanyakazi/export-pdf', [WafanyakaziController::class, 'exportPdf'])->name('wafanyakazi.export.pdf');
    
    // Masaplaya routes (boss only)
    Route::get('/masaplaya', [MasaplayaController::class, 'index'])->name('masaplaya.index');
    Route::post('/masaplaya', [MasaplayaController::class, 'store'])->name('masaplaya.store');
    Route::put('/masaplaya/{masaplaya}', [MasaplayaController::class, 'update'])->name('masaplaya.update');
    Route::delete('/masaplaya/{masaplaya}', [MasaplayaController::class, 'destroy'])->name('masaplaya.destroy');
    
    // Reports routes (boss only)
    Route::prefix('reports')->group(function () {
        Route::get('/select', [UserReportController::class, 'select'])->name('user.reports.select');
        Route::post('/generate', [UserReportController::class, 'generate'])->name('user.reports.generate');
        Route::post('/download', [UserReportController::class, 'download'])->name('user.reports.download');
    });
});

// =========================
// Admin routes
// =========================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Companies
        Route::get('/makampuni', [AdminController::class, 'makampuni'])->name('makampuni');

        // Reports - ALL in ONE group
        Route::prefix('reports')->group(function () {
            // Existing report routes
            Route::get('/', [MainReportController::class, 'index'])->name('reports');
            Route::get('/generate', [MainReportController::class, 'generate'])->name('reports.generate');
            Route::get('/export', [MainReportController::class, 'export'])->name('reports.export');
            Route::get('/download/{format}', [MainReportController::class, 'downloadCompaniesReport'])
                ->name('reports.download');
            Route::get('/download-companies', [MainReportController::class, 'downloadCompaniesReport'])
                ->name('reports.download-companies');

            // Company Activity Routes - INSIDE the same reports group
            Route::get('/company-activity', [CompanyActivityController::class, 'index'])
                ->name('reports.company-activity');
            
            Route::get('/company-activity/{id}/details', [CompanyActivityController::class, 'getCompanyDetails'])
                ->name('reports.company-activity.details');
        });
        // Change Password
        Route::get('/change-password', [AdminController::class, 'showChangePassword'])->name('password.show');
        Route::post('/change-password', [AdminController::class, 'updatePassword'])
            ->name('admin.password.update');
        
        // User & Company actions
        Route::post('/approve-user/{id}', [AdminController::class, 'approveUser'])->name('approveUser');
        Route::post('/company/{id}/verify', [AdminController::class, 'verifyCompany'])->name('verifyCompany');
        Route::delete('/company/{id}', [AdminController::class, 'destroyCompany'])->name('destroyCompany');
        Route::post('/companies/{id}/set-package', [AdminController::class, 'setPackageTime'])->name('setPackageTime');
        
        // Admin Payment Routes
        Route::resource('payments', App\Http\Controllers\Admin\PaymentController::class)
            ->only(['index', 'show']);
        Route::get('/company/{id}', [CompanyController::class, 'show'])->name('company.show');
        Route::post('/payments/{id}/verify', [App\Http\Controllers\Admin\PaymentController::class, 'verifyPayment'])
            ->name('payments.verify');
        Route::get('/payments/export', [App\Http\Controllers\Admin\PaymentController::class, 'export'])
            ->name('payments.export');
    });

// =========================
// API route for admin notifications
// =========================
Route::middleware(['auth', 'role:admin'])->group(function () {
    // API for new companies notification
    Route::get('/api/admin/new-companies', function() {
        // Get companies registered in the last 7 days
        $newCompanies = \App\Models\Company::with('user')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get([
                'id', 
                'company_name', 
                'owner_name', 
                'phone', 
                'email', 
                'is_verified',
                'is_user_approved',
                'package',
                'package_start',
                'package_end',
                'created_at'
            ]);
        
        return response()->json([
            'success' => true,
            'count' => $newCompanies->count(),
            'companies' => $newCompanies
        ]);
    })->name('api.admin.new-companies');
});

// =========================
// Payment routes (protected by auth)
// =========================
Route::middleware(['auth'])->group(function () {
    // Package selection and payment
    Route::get('/package-selection', [App\Http\Controllers\PaymentController::class, 'showPackageSelection'])
        ->name('payment.package.selection');
    
    Route::match(['get', 'post'], '/payment/form', [App\Http\Controllers\PaymentController::class, 'showPaymentForm'])
        ->name('payment.form');
    
    Route::post('/payment/process', [App\Http\Controllers\PaymentController::class, 'processPayment'])
        ->name('payment.process');
    
    Route::get('/payment/success/{reference}', [App\Http\Controllers\PaymentController::class, 'paymentSuccess'])
        ->name('payment.success');
    
    Route::get('/payment/failed', [App\Http\Controllers\PaymentController::class, 'paymentFailed'])
        ->name('payment.failed');
    
    Route::get('/payment/status/{reference}', [App\Http\Controllers\PaymentController::class, 'paymentStatus'])
        ->name('payment.status');
    
    Route::get('/payment/retry/{id}', [App\Http\Controllers\PaymentController::class, 'retryPayment'])
        ->name('payment.retry');
});

// =========================
// Debug route
// =========================
Route::get('/debug-payment-flow', function() {
    $user = Auth::user();
    if (!$user) {
        return redirect()->route('login');
    }
    
    // Log the referer
    Log::info('Debug payment flow accessed', [
        'url' => url()->previous(),
        'referer' => request()->headers->get('referer'),
        'user_id' => $user->id,
        'company_id' => $user->company_id
    ]);
    
    return response()->json([
        'message' => 'Debug endpoint',
        'previous_url' => url()->previous(),
        'referer' => request()->headers->get('referer'),
        'user' => $user->only(['id', 'name', 'email', 'role']),
        'company' => $user->company ? $user->company->only(['id', 'company_name', 'package_end']) : null,
        'session' => session()->all()
    ]);
})->name('debug.payment.flow');

// =========================
// PesaPal callbacks (public - no auth)
// =========================
Route::get('/pesapal/callback', [App\Http\Controllers\PaymentController::class, 'callback'])
    ->name('pesapal.callback');

Route::post('/pesapal/ipn', [App\Http\Controllers\PaymentController::class, 'ipn'])
    ->name('pesapal.ipn');

Route::get('/pesapal/ipn', [App\Http\Controllers\PaymentController::class, 'ipn']);