<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('patients.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Financial routes
    Route::get('/debtors', function () {
        return view('financial.debtors');
    })->name('financial.debtors');
});

require __DIR__.'/auth.php';
require __DIR__.'/../routes/patients.php';
require __DIR__.'/../routes/subscription.php';
require __DIR__.'/../routes/appointments.php';