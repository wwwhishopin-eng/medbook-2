<?php

use App\Http\Controllers\SmsWebhookController;
use Illuminate\Support\Facades\Route;

// Public webhook routes (no auth required for SMS provider callbacks)
Route::post('/webhook/sms/confirmation', [SmsWebhookController::class, 'handleConfirmation'])
    ->name('webhook.sms.confirmation');

// Public waiting room display (no auth required - shown on screen in waiting room)
Route::get('/waiting-room', function () {
    return view('queue.waiting-room');
})->name('waiting-room.display');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Quick booking
    Route::get('/appointments/quick', function () {
        return view('appointments.quick');
    })->name('appointments.quick');

    // Calendar views
    Route::get('/appointments/calendar', function () {
        return view('appointments.calendar');
    })->name('appointments.calendar');

    // Waiting list
    Route::get('/waiting-list', function () {
        return view('waiting-list.index');
    })->name('waiting-list.index');

    // Queue management
    Route::get('/queue', function () {
        return view('queue.index');
    })->name('queue.index');

    // Send confirmation request manually
    Route::post('/appointments/{appointment}/send-confirmation', [SmsWebhookController::class, 'sendConfirmationRequest'])
        ->name('appointments.send-confirmation');

});
