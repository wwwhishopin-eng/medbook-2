<?php

use App\Http\Controllers\OnlineBookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('patients.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public booking routes
Route::get('/booking/{slug}', [OnlineBookingController::class, 'show'])->name('booking.show');
Route::get('/booking/{slug}/slots', [OnlineBookingController::class, 'getAvailableSlots'])->name('booking.slots');
Route::post('/booking/{slug}', [OnlineBookingController::class, 'book'])->name('booking.book');
Route::get('/booking/{slug}/success', [OnlineBookingController::class, 'success'])->name('booking.success');

// Push notification routes
Route::middleware('auth')->group(function () {
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::get('/push/status', [PushSubscriptionController::class, 'status'])->name('push.status');
});
Route::get('/push/vapid-key', [PushSubscriptionController::class, 'vapidPublicKey'])->name('push.vapid-key');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Financial routes
    Route::get('/debtors', function () {
        return view('financial.debtors');
    })->name('financial.debtors');

    // Audit log
    Route::get('/audit', function () {
        return view('audit.index');
    })->name('audit.index');
});

require __DIR__.'/auth.php';
require __DIR__.'/../routes/patients.php';
require __DIR__.'/../routes/subscription.php';
require __DIR__.'/../routes/appointments.php';