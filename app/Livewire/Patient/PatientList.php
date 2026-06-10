<?php

namespace App\Livewire\Patient;

use App\Helpers\Persian;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PatientList extends Component
{
    use WithPagination;

    // ── Bound to URL so search/filter survive page refresh ───────────────────
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    #[Url(as: 'sort')]
    public string $sortField = 'created_at';

    #[Url(as: 'dir')]
    public string $sortDir = 'desc';

    public int $perPage = 15;

    // ── Modal state ──────────────────────────────────────────────────────────
    public bool $confirmingDelete = false;
    public ?int $deletingId = null;

    protected $queryString = ['search', 'statusFilter', 'sortField', 'sortDir'];

    // ── Lifecycle ────────────────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    // ── Sorting ──────────────────────────────────────────────────────────────

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
    }

    // ── Delete ───────────────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $this->confirmingDelete = true;
        $this->deletingId = $id;
    }

    public function deletePatient(): void
    {
        $patient = Patient::findOrFail($this->deletingId);
        $name    = $patient->full_name;
        $patient->delete();

        $this->confirmingDelete = false;
        $this->deletingId       = null;

        $this->dispatch('notify', message: "بیمار «{$name}» حذف شد.", type: 'success');
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
        $this->deletingId       = null;
    }

    // ── Refresh ──────────────────────────────────────────────────────────────

    #[On('patient-saved')]
    public function refresh(): void
    {
        // Triggered after create/edit modals close
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $allowedSortFields = ['first_name', 'last_name', 'date_of_birth', 'status', 'created_at'];
        $sortField = in_array($this->sortField, $allowedSortFields) ? $this->sortField : 'created_at';

        $searchTerm = Persian::toWestern($this->search);

        $patients = Patient::query()
            ->when($searchTerm, fn ($q) => $q->search($searchTerm))
            ->when($this->statusFilter, fn ($q) => $q->status($this->statusFilter))
            ->orderBy($sortField, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.patient.patient-list', [
            'patients' => $patients,
            'statuses' => Patient::STATUSES,
        ]);
    }
}