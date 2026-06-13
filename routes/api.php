<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAppointmentController;
use App\Http\Controllers\Api\ApiFinancialController;
use App\Http\Controllers\Api\ApiPatientController;
use App\Http\Controllers\Api\ApiReportController;
use Illuminate\Support\Facades\Route;

// Auth
Route::post('/login', [ApiAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/me', [ApiAuthController::class, 'me']);

    // Patients
    Route::apiResource('patients', ApiPatientController::class);
    Route::get('patients-search', [ApiPatientController::class, 'search']);

    // Appointments
    Route::apiResource('appointments', ApiAppointmentController::class);
    Route::get('appointments/today', [ApiAppointmentController::class, 'today']);
    Route::post('appointments/{appointment}/cancel', [ApiAppointmentController::class, 'cancel']);
    Route::get('available-slots', [ApiAppointmentController::class, 'availableSlots']);

    // Financial
    Route::get('transactions', [ApiFinancialController::class, 'transactions']);
    Route::post('transactions', [ApiFinancialController::class, 'storeTransaction']);
    Route::get('debtors', [ApiFinancialController::class, 'debtorList']);

    // Reports
    Route::get('dashboard-stats', [ApiReportController::class, 'dashboard']);
    Route::get('appointment-stats', [ApiReportController::class, 'appointmentStats']);
});
