<?php

use App\Http\Controllers\SmsWebhookController;
use App\Livewire\Appointment\QuickBooking;
use Illuminate\Support\Facades\Route;

// Public webhook routes (no auth required for SMS provider callbacks)
Route::post('/webhook/sms/confirmation', [SmsWebhookController::class, 'handleConfirmation'])
    ->name('webhook.sms.confirmation');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Quick booking
    Route::get('/appointments/quick', function () {
        return view('appointments.quick');
    })->name('appointments.quick');

    // Waiting list
    Route::get('/waiting-list', function () {
        return view('waiting-list.index');
    })->name('waiting-list.index');

    // Send confirmation request manually
    Route::post('/appointments/{appointment}/send-confirmation', [SmsWebhookController::class, 'sendConfirmationRequest'])
        ->name('appointments.send-confirmation');

});
