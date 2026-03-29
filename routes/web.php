<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckInController;

Route::get('/', function () {
    return redirect()->route('checkin.step1');
});

Route::get('/lang/{locale}', [CheckInController::class, 'setLocale'])->name('set.locale');

Route::middleware([\App\Http\Middleware\LocaleMiddleware::class])->group(function() {
    Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin.step1');
    Route::post('/checkin/process', [CheckInController::class, 'processImages'])->name('checkin.process');
    Route::get('/checkin/form', [CheckInController::class, 'form'])->name('checkin.step2');
    Route::post('/checkin/store', [CheckInController::class, 'store'])->name('checkin.store');
    
    Route::view('/checkin/success', 'checkin.success')->name('checkin.success');
});
