<?php

namespace App\Livewire\Financial;

use App\Helpers\Persian;
use App\Models\Patient;
use App\Models\Transaction;
use App\Services\SMS\SmsService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class DebtorList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'debt_desc';

    public function updatingSearch(): void { $this->resetPage(); }

    public function sendDebtReminder(int $patientId, SmsService $smsService): void
    {
        $patient = Patient::find($patientId);
        if (!$patient || !$patient->phone) return;

        $debt = Transaction::getDebtForPatient($patientId);
        $amount = Persian::currency($debt);

        $smsService->send(
            $patient->phone,
            "بیمار گرامی، بدهی شما به مبلغ {$amount} معوق می‌باشد. لطفاً جهت پرداخت با مطب تماس بگیرید."
        );

        $this->dispatch('notify', message: "پیامک یادآوری بدهی برای {$patient->full_name} ارسال شد.", type: 'success');
    }

    public function render(): View
    {
        $debtors = Patient::query()
            ->with('transactions')
            ->whereHas('transactions')
            ->when($this->search, function ($q) {
                $term = Persian::toWestern($this->search);
                $q->search($term);
            })
            ->get()
            ->filter(fn($p) => $p->debt > 0)
            ->sortByDesc('debt')
            ->values();

        if ($this->sortBy === 'name_asc') {
            $debtors = $debtors->sortBy('full_name')->values();
        } elseif ($this->sortBy === 'debt_asc') {
            $debtors = $debtors->sortBy('debt')->values();
        }

        $totalDebt = $debtors->sum('debt');

        return view('livewire.financial.debtor-list', [
            'debtors' => $debtors,
            'totalDebt' => $totalDebt,
        ]);
    }
}
