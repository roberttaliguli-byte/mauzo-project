<?php

use App\Http\Controllers\StockTakeController;
use App\Http\Controllers\ProductCleanupController;
use Illuminate\Support\Facades\Route;

// ================================
// Stock Taking (Boss + "mkubwa" employees)
// ================================
Route::prefix('stock-take')->name('stocktake.')->group(function () {
    Route::get('/', [StockTakeController::class, 'index'])->name('index');
    Route::post('/start', [StockTakeController::class, 'start'])->name('start');

    Route::get('/{id}', [StockTakeController::class, 'show'])->whereNumber('id')->name('show');
    Route::get('/{id}/items', [StockTakeController::class, 'items'])->whereNumber('id')->name('items');
    Route::post('/{id}/items/{itemId}/count', [StockTakeController::class, 'count'])->whereNumber(['id', 'itemId'])->name('count');
    Route::post('/{id}/sync', [StockTakeController::class, 'sync'])->whereNumber('id')->name('sync');

    Route::post('/{id}/unmatched', [StockTakeController::class, 'storeUnmatched'])->whereNumber('id')->name('unmatched.store');
    Route::delete('/{id}/unmatched/{rowId}', [StockTakeController::class, 'destroyUnmatched'])->whereNumber(['id', 'rowId'])->name('unmatched.destroy');

    Route::get('/{id}/report', [StockTakeController::class, 'report'])->whereNumber('id')->name('report');
    Route::post('/{id}/resolve', [StockTakeController::class, 'resolve'])->whereNumber('id')->name('resolve');
    Route::post('/{id}/close', [StockTakeController::class, 'close'])->whereNumber('id')->name('close');
    Route::post('/{id}/apply-stock', [StockTakeController::class, 'applyStock'])->whereNumber('id')->name('apply-stock');
    Route::post('/{id}/cancel', [StockTakeController::class, 'cancel'])->whereNumber('id')->name('cancel');

    Route::get('/{id}/compare/{with?}', [StockTakeController::class, 'compare'])->whereNumber(['id', 'with'])->name('compare');
});

// ================================
// Product Cleanup (BOSS ONLY - web guard + server-side password)
// ================================
Route::prefix('product-cleanup')->name('cleanup.')->middleware([BossOnly::class])->group(function () {
    Route::get('/', [ProductCleanupController::class, 'index'])->name('index');
    Route::post('/unlock', [ProductCleanupController::class, 'unlock'])->middleware('throttle:10,1')->name('unlock');
    Route::post('/lock', [ProductCleanupController::class, 'lock'])->name('lock');
    Route::get('/data', [ProductCleanupController::class, 'data'])->name('data');
    Route::post('/preview', [ProductCleanupController::class, 'preview'])->name('preview');
    Route::post('/execute', [ProductCleanupController::class, 'execute'])->middleware('throttle:10,1')->name('execute');
    Route::post('/restore', [ProductCleanupController::class, 'restore'])->name('restore');
    Route::get('/log', [ProductCleanupController::class, 'log'])->name('log');
});