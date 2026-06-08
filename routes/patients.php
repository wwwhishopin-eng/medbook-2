<?php

use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::resource('patients', PatientController::class);

    Route::get('patients/{patient}/history', [PatientController::class, 'history'])
         ->name('patients.history');

    Route::get('api/patients/search', function (\Illuminate\Http\Request $request) {
        $term = $request->string('q')->trim();

        if ($term->length() < 2) {
            return response()->json([]);
        }

        return \App\Models\Patient::search((string) $term)
            ->select('id', 'first_name', 'last_name', 'phone', 'avatar_color', 'status')
            ->limit(8)
            ->get()
            ->map(fn ($p) => [
                'id'    => $p->id,
                'name'  => $p->full_name,
                'phone' => $p->phone,
                'code'  => $p->code,
                'url'   => route('patients.show', $p),
            ]);
    })->name('api.patients.search');

});