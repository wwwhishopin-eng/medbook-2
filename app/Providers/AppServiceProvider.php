<?php

namespace App\Providers;

use App\Livewire\Patient\PatientForm;
use App\Livewire\Patient\PatientHistory;
use App\Livewire\Patient\PatientList;
use App\Livewire\Patient\PatientProfile;
use App\Livewire\Patient\PatientSearch;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}


    public function boot(): void
    {
        Livewire::component('patient.patient-list',    \App\Livewire\Patient\PatientList::class);
        Livewire::component('patient.patient-form',    \App\Livewire\Patient\PatientForm::class);
        Livewire::component('patient.patient-profile', \App\Livewire\Patient\PatientProfile::class);
        Livewire::component('patient.patient-search',  \App\Livewire\Patient\PatientSearch::class);
        Livewire::component('patient.patient-history', \App\Livewire\Patient\PatientHistory::class);
    }
}