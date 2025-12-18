<?php

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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController as MainReportController;
use App\Http\Controllers\UserReportController;

Route::prefix('user')->group(function () {
    // Page to select report type & time period
    Route::get('reports/select', [UserReportController::class, 'select'])
        ->name('user.reports.select');

    // Download PDF
    Route::get('reports/download', [UserReportController::class, 'download'])
        ->name('user.reports.download');
});

// ========================
// Admin routes (with middleware)
// =========================
Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Companies
    Route::get('/makampuni', [AdminController::class, 'makampuni'])->name('makampuni');

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [MainReportController::class, 'index'])->name('reports');
        Route::get('/generate', [MainReportController::class, 'generate'])->name('reports.generate');
        Route::get('/export', [MainReportController::class, 'export'])->name('reports.export');

        // Download Reports (Excel/PDF)
        Route::get('/download/{format}', [MainReportController::class, 'downloadCompaniesReport'])
            ->name('reports.download');
        Route::get('/download-companies', [MainReportController::class, 'downloadCompaniesReport'])
            ->name('reports.download-companies');
    });



    // Change Password
    Route::get('/change-password', [AdminController::class, 'showChangePassword'])->name('password.show');
    Route::post('/change-password', [AdminController::class, 'updatePassword'])->name('password.update');

    // Approve user
    Route::post('/approve-user/{id}', [AdminController::class, 'approveUser'])->name('approveUser');

    // Verify company
    Route::post('/company/{id}/verify', [AdminController::class, 'verifyCompany'])->name('verifyCompany');

    // Delete company
    Route::delete('/company/{id}', [AdminController::class, 'destroy'])->name('destroyCompany');

    // Set package time
    Route::post('/companies/{id}/set-package', [AdminController::class, 'setPackageTime'])->name('setPackageTime');
});

// =========================
// Public routes
// =========================
Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'registerPost'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');

// Email Verification Link
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])
    ->name('verify.email');

    
// =========================
// Protected routes (auth middleware)
// =========================
Route::middleware('auth')->group(function () {
    // Common routes for all authenticated users
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::put('/company/update', [CompanyController::class, 'update'])->name('company.update');
    Route::get('/password/change', [PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/password/update', [PasswordController::class, 'update'])->name('password.update');
    Route::get('/company/info', [ProfileController::class, 'companyInfo'])->name('company.info');

    // =========================
    // Boss routes
    // =========================
    Route::middleware('role:boss')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/mauzo', [MauzoController::class, 'index'])->name('mauzo.index');
        Route::get('/madeni', [MadeniController::class, 'index'])->name('madeni.index');
        Route::get('/matumizi', [MatumiziController::class, 'index'])->name('matumizi.index');
        Route::get('/bidhaa', [BidhaaController::class, 'index'])->name('bidhaa.index');
        Route::get('/manunuzi', [ManunuziController::class, 'index'])->name('manunuzi.index');
        Route::get('/wafanyakazi', [WafanyakaziController::class, 'index'])->name('wafanyakazi.index');
        Route::get('/masaplaya', [MasaplayaController::class, 'index'])->name('masaplaya.index');
        Route::get('/wateja', [MtejaController::class, 'index'])->name('wateja.index');
        Route::get('/uchambuzi', [UchambuziController::class, 'index'])->name('uchambuzi.index');
    });

    // =========================
    // Mfanyakazi routes
    // =========================
    Route::middleware('role:mfanyakazi')->group(function () {
        Route::get('/mfanyakazi/dashboard', [DashboardController::class, 'mfanyakaziDashboard'])->name('mfanyakazi.dashboard');

        // Pages allowed for Mfanyakazi
        Route::get('/mauzo', [MauzoController::class, 'index'])->name('mauzo.index');
        Route::get('/madeni', [MadeniController::class, 'index'])->name('madeni.index');
        Route::get('/matumizi', [MatumiziController::class, 'index'])->name('matumizi.index');
        Route::get('/bidhaa', [BidhaaController::class, 'index'])->name('bidhaa.index');
        Route::get('/wateja', [MtejaController::class, 'index'])->name('wateja.index');
    });
});

    // =========================
    // Mfanyakazi routes
    // =========================
    Route::middleware('role:mfanyakazi')->group(function () {
        Route::get('/mfanyakazi/dashboard', [DashboardController::class, 'index'])->name('mfanyakazi.dashboard');

        // Only the pages allowed for Mfanyakazi
        Route::get('/mauzo', [MauzoController::class, 'index'])->name('mauzo.index');
        Route::get('/madeni', [MadeniController::class, 'index'])->name('madeni.index');
        Route::get('/matumizi', [MatumiziController::class, 'index'])->name('matumizi.index');
        Route::get('/bidhaa', [BidhaaController::class, 'index'])->name('bidhaa.index');
        Route::get('/wateja', [MtejaController::class, 'index'])->name('wateja.index');

        
    });




// Wateja Routes
// ================================
Route::get('/wateja', [MtejaController::class, 'index'])->name('wateja.index');
Route::post('/wateja', [MtejaController::class, 'store'])->name('wateja.store');
Route::put('/wateja/{mteja}', [MtejaController::class, 'update'])->name('wateja.update');
Route::delete('/wateja/{mteja}', [MtejaController::class, 'destroy'])->name('wateja.destroy');

// ================================
// Matumizi Routes
// ================================
Route::get('/matumizi', [MatumiziController::class, 'index'])->name('matumizi.index');
Route::post('/matumizi', [MatumiziController::class, 'store'])->name('matumizi.store');
Route::put('/matumizi/{id}', [MatumiziController::class, 'update'])->name('matumizi.update');
Route::delete('/matumizi/{id}', [MatumiziController::class, 'destroy'])->name('matumizi.destroy');
// Matumizi Routes
Route::resource('matumizi', MatumiziController::class);
Route::post('/matumizi/sajili-aina', [MatumiziController::class, 'sajiliAina'])->name('matumizi.sajili-aina');
Route::delete('/matumizi/aina/{id}', [MatumiziController::class, 'destroyAina'])->name('matumizi.destroy-aina');

// ================================
// Wafanyakazi Routes
// ================================
Route::get('/wafanyakazi', [WafanyakaziController::class, 'index'])->name('wafanyakazi.index');
Route::post('/wafanyakazi', [WafanyakaziController::class, 'store'])->name('wafanyakazi.store');
Route::get('/wafanyakazi/{id}/edit', [WafanyakaziController::class, 'edit'])->name('wafanyakazi.edit');
Route::put('/wafanyakazi/{id}', [WafanyakaziController::class, 'update'])->name('wafanyakazi.update');
Route::delete('/wafanyakazi/{id}', [WafanyakaziController::class, 'destroy'])->name('wafanyakazi.destroy');

Route::get('/wafanyakazi/export-pdf', [WafanyakaziController::class, 'exportPdf'])->name('wafanyakazi.export.pdf');

// ================================
// Mauzo Routes
// ================================
Route::get('/mauzo', [MauzoController::class, 'index'])->name('mauzo.index');
Route::post('/mauzo', [MauzoController::class, 'store'])->name('mauzo.store');
Route::delete('/mauzo/{mauzo}', [MauzoController::class, 'destroy'])->name('mauzo.destroy');
Route::post('/mauzo/kikapu/store', [MauzoController::class, 'storeKikapu']) ->name('mauzo.store.kikapu');
Route::post('/mauzo/kikapu/kopesha', [MauzoController::class, 'storeKikapuLoan'])->name('mauzo.store.kikapu.loan');
Route::post('/mauzo/kopesha', [MauzoController::class, 'storeKikapuLoan'])
    ->name('mauzo.store.kopesha');

    
// ✅ Hifadhi Mauzo kwa kutumia Barcode
Route::post('/mauzo/barcode', [MauzoController::class, 'storeBarcode'])->name('mauzo.store.barcode');


// ================================
// Masaplaya Routes
// ================================
Route::get('/masaplaya', [MasaplayaController::class, 'index'])->name('masaplaya.index');
Route::post('/masaplaya', [MasaplayaController::class, 'store'])->name('masaplaya.store');
Route::put('/masaplaya/{masaplaya}', [MasaplayaController::class, 'update'])->name('masaplaya.update');
Route::delete('/masaplaya/{masaplaya}', [MasaplayaController::class, 'destroy'])->name('masaplaya.destroy');

// ================================
// 🚀 Bidhaa Routes
// ================================

Route::get('/bidhaa', [BidhaaController::class, 'index'])->name('bidhaa.index');
Route::post('/bidhaa', [BidhaaController::class, 'store'])->name('bidhaa.store');
Route::put('/bidhaa/{id}', [BidhaaController::class, 'update'])->name('bidhaa.update');
Route::delete('/bidhaa/{id}', [BidhaaController::class, 'destroy'])->name('bidhaa.destroy');
Route::get('/bidhaa/download-sample', [BidhaaController::class, 'downloadSample'])->name('bidhaa.downloadSample');
Route::post('/bidhaa/upload-csv', [BidhaaController::class, 'uploadCSV'])->name('bidhaa.uploadCSV');

// ✅ Hifadhi bidhaa kwa kutumia barcode
Route::post('/bidhaa/barcode', [BidhaaController::class, 'storeBarcode'])->name('bidhaa.store.barcode');

// ✅ Tafuta bidhaa kwa barcode (kwa Mauzo page / API)
Route::get('/bidhaa/tafuta-barcode/{barcode}', [BidhaaController::class, 'tafutaBarcode'])
    ->name('bidhaa.tafuta.barcode');

// ================================
// Manunuzi Routes
// ================================
Route::get('/manunuzi', [ManunuziController::class, 'index'])->name('manunuzi.index');
Route::post('/manunuzi', [ManunuziController::class, 'store'])->name('manunuzi.store');
Route::put('/manunuzi/{manunuzi}', [ManunuziController::class, 'update'])->name('manunuzi.update');
Route::delete('/manunuzi/{manunuzi}', [ManunuziController::class, 'destroy'])->name('manunuzi.destroy');

// ================================
// Madeni Routes
// ================================
Route::get('/madeni', [MadeniController::class, 'index'])->name('madeni.index');
Route::post('/madeni', [MadeniController::class, 'store'])->name('madeni.store');

Route::resource('madeni', MadeniController::class);

Route::put('/madeni/{madeni}', [MadeniController::class, 'update'])->name('madeni.update');
Route::post('/madeni/{madeni}/rejesha', [MadeniController::class, 'rejesha'])->name('madeni.rejesha');
Route::delete('/madeni/{madeni}', [MadeniController::class, 'destroy'])->name('madeni.destroy');

// ================================
// Uchambuzi Routes
Route::get('/uchambuzi', [UchambuziController::class, 'index'])->name('uchambuzi.index');
Route::get('/mwenendo', [UchambuziController::class, 'mwenendoRange']);
// ================================
