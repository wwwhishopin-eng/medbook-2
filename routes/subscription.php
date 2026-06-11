<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/subscription', function () {
        return view('subscription.show');
    })->name('subscription.show');

    Route::post('/subscription/renew', [SubscriptionController::class, 'renew'])
        ->name('subscription.renew');

    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])
        ->name('subscription.cancel');
});
