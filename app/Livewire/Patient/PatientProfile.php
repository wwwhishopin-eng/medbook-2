<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PatientProfile extends Component
{
    public Patient $patient;

    public string $activeTab = 'overview';

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render(): View
    {
        $this->patient->load([
            'medicalHistory' => fn ($q) => $q->with('doctor')->latest()->limit(20),
            'appointments'   => fn ($q) => $q->latest()->limit(10),
            'prescriptions'  => fn ($q) => $q->latest()->limit(10),
        ]);

        return view('livewire.patient.patient-profile', [
            'patient' => $this->patient,
        ]);
    }
}