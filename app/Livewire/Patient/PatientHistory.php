<?php

namespace App\Livewire\Patient;

use App\Models\MedicalHistory;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PatientHistory extends Component
{
    use WithPagination;

    public Patient $patient;

    public string $visitTypeFilter = '';
    public string $dateFrom        = '';
    public string $dateTo          = '';

    // ── Add history entry ────────────────────────────────────────────────────
    public bool   $showForm         = false;
    public string $visit_date       = '';
    public string $visit_type       = 'follow_up';
    public string $chief_complaint  = '';
    public string $diagnosis        = '';
    public string $treatment        = '';
    public string $prescriptions    = '';
    public string $doctor_notes     = '';
    public string $follow_up_date   = '';

    public function mount(Patient $patient): void
    {
        $this->patient    = $patient;
        $this->visit_date = now()->format('Y-m-d');
    }

    public function updatingVisitTypeFilter(): void { $this->resetPage(); }
    public function updatingDateFrom(): void        { $this->resetPage(); }
    public function updatingDateTo(): void          { $this->resetPage(); }

    public function openForm(): void
    {
        $this->showForm = true;
    }

    public function saveEntry(): void
    {
        $validated = $this->validate([
            'visit_date'      => ['required', 'date'],
            'visit_type'      => ['required', 'string'],
            'chief_complaint' => ['nullable', 'string', 'max:255'],
            'diagnosis'       => ['nullable', 'string', 'max:1000'],
            'treatment'       => ['nullable', 'string', 'max:1000'],
            'prescriptions'   => ['nullable', 'string', 'max:1000'],
            'doctor_notes'    => ['nullable', 'string', 'max:2000'],
            'follow_up_date'  => ['nullable', 'date', 'after:visit_date'],
        ], [
            'visit_date.required' => 'تاریخ ویزیت الزامی است.',
            'follow_up_date.after'=> 'تاریخ پیگیری باید بعد از تاریخ ویزیت باشد.',
        ]);

        $this->patient->medicalHistory()->create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        $this->showForm       = false;
        $this->chief_complaint = $this->diagnosis = $this->treatment = '';
        $this->prescriptions   = $this->doctor_notes = $this->follow_up_date = '';
        $this->visit_date      = now()->format('Y-m-d');
        $this->visit_type      = 'follow_up';

        $this->dispatch('notify', message: 'سابقه پزشکی با موفقیت ثبت شد.', type: 'success');
    }

    public function render(): View
    {
        $history = $this->patient->medicalHistory()
            ->with('doctor')
            ->when($this->visitTypeFilter, fn ($q) => $q->where('visit_type', $this->visitTypeFilter))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('visit_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('visit_date', '<=', $this->dateTo))
            ->paginate(8);

        return view('livewire.patient.patient-history', [
            'history'     => $history,
            'visitTypes'  => MedicalHistory::VISIT_TYPES,
        ]);
    }
}