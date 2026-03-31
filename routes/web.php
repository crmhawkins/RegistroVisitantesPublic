<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckInController;

Route::get('/', function () {
    return redirect()->route('checkin.step1');
});

Route::get('/lang/{locale}', [CheckInController::class, 'setLocale'])->name('set.locale');

Route::middleware([\App\Http\Middleware\LocaleMiddleware::class])->group(function() {
    Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin.step1');
    Route::get('/checkin/form', [CheckInController::class, 'form'])->name('checkin.step2');
    Route::get('/checkin/success', [CheckInController::class, 'success'])->name('checkin.success');

    Route::middleware('throttle:20,1')->group(function () {
        Route::post('/checkin/process', [CheckInController::class, 'processImages'])->name('checkin.process');
        Route::post('/checkin/store', [CheckInController::class, 'store'])->name('checkin.store');
    });
});
