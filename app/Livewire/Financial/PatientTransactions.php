<?php

namespace App\Livewire\Financial;

use App\Helpers\Persian;
use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PatientTransactions extends Component
{
    use WithPagination;

    public ?Patient $patient = null;
    public string $search = '';
    public string $typeFilter = '';

    // Add transaction form
    public bool $showForm = false;
    public string $form_type = 'charge';
    public string $form_description = '';
    public string $form_amount = '';
    public string $form_date = '';
    public string $form_notes = '';

    public function mount(?Patient $patient = null): void
    {
        $this->patient = $patient;
        $this->form_date = now()->format('Y-m-d');
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingTypeFilter(): void { $this->resetPage(); }

    public function openForm(): void
    {
        $this->showForm = true;
        $this->form_date = now()->format('Y-m-d');
    }

    public function saveTransaction(): void
    {
        $this->validate([
            'form_type' => 'required|in:charge,payment',
            'form_description' => 'required|string|max:255',
            'form_amount' => 'required|integer|min:1',
            'form_date' => 'required|date',
        ], [
            'form_type.required' => 'نوع تراکنش الزامی است.',
            'form_description.required' => 'شرح تراکنش الزامی است.',
            'form_amount.required' => 'مبلغ الزامی است.',
            'form_amount.min' => 'مبلغ باید بزرگتر از صفر باشد.',
            'form_date.required' => 'تاریخ الزامی است.',
        ]);

        if (!$this->patient) {
            $this->addError('patient', 'بیمار مشخص نشده است.');
            return;
        }

        Transaction::create([
            'patient_id' => $this->patient->id,
            'type' => $this->form_type,
            'description' => $this->form_description,
            'amount' => (int) Persian::toWestern($this->form_amount),
            'transaction_date' => $this->form_date,
            'notes' => $this->form_notes ?: null,
            'created_by' => auth()->id(),
        ]);

        $this->showForm = false;
        $this->form_type = 'charge';
        $this->form_description = '';
        $this->form_amount = '';
        $this->form_notes = '';

        $this->dispatch('notify', message: 'تراکنش با موفقیت ثبت شد.', type: 'success');
    }

    public function render(): View
    {
        $query = Transaction::query()
            ->with('creator')
            ->when($this->patient, fn($q) => $q->where('patient_id', $this->patient->id))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter));

        if (!$this->patient && $this->search) {
            $term = Persian::toWestern($this->search);
            $query->whereHas('patient', fn($q) => $q->search($term));
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('livewire.financial.patient-transactions', [
            'transactions' => $transactions,
        ]);
    }
}
