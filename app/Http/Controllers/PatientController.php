<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PatientController extends Controller
{

    /**
     * Full patient list (server-rendered; Livewire handles search/filter).
     */
    public function index(): View
    {
        return view('patients.index');
    }

    /**
     * Show the form to create a new patient.
     */
    public function create(): View
    {
        return view('patients.create', [
            'statuses'   => Patient::STATUSES,
            'bloodTypes' => Patient::BLOOD_TYPES,
        ]);
    }

    /**
     * Store a new patient.
     */
    public function store(StorePatientRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['avatar_color'] = Patient::randomAvatarColor();

        $patient = Patient::create($data);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', "بیمار «{$patient->full_name}» با موفقیت اضافه شد.");
    }

    /**
     * Display the patient profile.
     */
    public function show(Patient $patient): View
    {
        $patient->load(['appointments' => fn ($q) => $q->latest()->limit(5)]);

        $historyCount    = $patient->medicalHistory()->count();
        $lastVisit       = $patient->medicalHistory()->first();
        $upcomingCount   = $patient->appointments()->upcoming()->count();

        return view('patients.show', compact('patient', 'historyCount', 'lastVisit', 'upcomingCount'));
    }

    /**
     * Show edit form.
     */
    public function edit(Patient $patient): View
    {
        return view('patients.edit', [
            'patient'    => $patient,
            'statuses'   => Patient::STATUSES,
            'bloodTypes' => Patient::BLOOD_TYPES,
        ]);
    }

    /**
     * Update a patient.
     */
    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $patient->update($request->validated());

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', "اطلاعات بیمار «{$patient->full_name}» به‌روزرسانی شد.");
    }

    /**
     * Soft-delete a patient.
     */
    public function destroy(Patient $patient): RedirectResponse
    {
        $name = $patient->full_name;
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', "بیمار «{$name}» حذف شد.");
    }

    /**
     * Show paginated medical history for a patient.
     */
    public function history(Patient $patient): View
    {
        $history = $patient->medicalHistory()->with('doctor')->paginate(10);

        return view('patients.history', compact('patient', 'history'));
    }
}