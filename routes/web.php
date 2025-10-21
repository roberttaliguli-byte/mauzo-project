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

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::put('/company/update', [CompanyController::class, 'update'])->name('company.update');


Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'registerPost'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');




Route::middleware('auth')->group(function () {
    Route::get('/password/change', [ProfileController::class, 'editPassword'])->name('password.change');
    Route::get('/company/info', [ProfileController::class, 'companyInfo'])->name('company.info');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/password/change', [PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/password/update', [PasswordController::class, 'update'])->name('password.update');
});

// protected route example
Route::get('/dashboard', function() {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/', function () {
return redirect('/login');
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

// ================================
// Wafanyakazi Routes
// ================================
Route::get('/wafanyakazi', [WafanyakaziController::class, 'index'])->name('wafanyakazi.index');
Route::post('/wafanyakazi', [WafanyakaziController::class, 'store'])->name('wafanyakazi.store');
Route::get('/wafanyakazi/{id}/edit', [WafanyakaziController::class, 'edit'])->name('wafanyakazi.edit');
Route::put('/wafanyakazi/{id}', [WafanyakaziController::class, 'update'])->name('wafanyakazi.update');
Route::delete('/wafanyakazi/{id}', [WafanyakaziController::class, 'destroy'])->name('wafanyakazi.destroy');

// ================================
// Mauzo Routes
// ================================
Route::get('/mauzo', [MauzoController::class, 'index'])->name('mauzo.index');
Route::post('/mauzo', [MauzoController::class, 'store'])->name('mauzo.store');
Route::delete('/mauzo/{mauzo}', [MauzoController::class, 'destroy'])->name('mauzo.destroy');
Route::post('/mauzo/kikapu/store', [MauzoController::class, 'storeKikapu']) ->name('mauzo.store.kikapu');
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
Route::post('/madeni/{madeni}/rejesha', [MadeniController::class, 'rejesha'])->name('madeni.rejesha');
Route::delete('/madeni/{madeni}', [MadeniController::class, 'destroy'])->name('madeni.destroy');
Route::resource('madeni', MadeniController::class);


// ================================
// Uchambuzi Routes
Route::get('/uchambuzi', [UchambuziController::class, 'index'])->name('uchambuzi.index');
Route::get('/mwenendo', [UchambuziController::class, 'mwenendoRange']);
// ================================
