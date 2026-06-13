<?php

namespace App\Livewire\Patient;

use App\Models\MedicalHistory;
use App\Models\Appointment;
use App\Models\Patient;
use App\Services\FollowUpSuggestionService;
use App\Services\SlotSuggestionService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class FollowUpSuggestion extends Component
{
    public Patient $patient;
    public int $historyId = 0;
    public ?array $suggestion = null;
    public bool $show = false;
    public string $selectedSlot = '';

    private FollowUpSuggestionService $followUpService;
    private SlotSuggestionService $slotService;

    public function boot(FollowUpSuggestionService $followUpService, SlotSuggestionService $slotService): void
    {
        $this->followUpService = $followUpService;
        $this->slotService = $slotService;
    }

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
        $latest = $patient->medicalHistory()->latest()->first();
        if ($latest) {
            $this->historyId = $latest->id;
            $this->checkForFollowUp();
        }
    }

    public function checkForFollowUp(): void
    {
        $history = MedicalHistory::find($this->historyId);

        if (!$history) {
            return;
        }

        $suggestion = $this->followUpService->suggestFollowUpAppointment($history, $this->slotService);

        if ($suggestion) {
            $this->suggestion = $suggestion;
            $this->show = true;
        }
    }

    public function bookFollowUp(): void
    {
        if (!$this->selectedSlot && !isset($this->suggestion['available_slot'])) {
            $this->addError('slot', 'لطفاً ساعت نوبت را انتخاب کنید.');
            return;
        }

        $history = MedicalHistory::find($this->historyId);
        if (!$history) {
            return;
        }

        $slot = $this->selectedSlot ?: $this->suggestion['available_slot']['datetime'];
        $startAt = \Carbon\Carbon::parse($slot);
        $endAt = $startAt->copy()->addMinutes(30);

        Appointment::create([
            'patient_id' => $history->patient_id,
            'created_by' => auth()->id(),
            'title' => 'نوبت پیگیری',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => Appointment::STATUS_RESERVED,
            'type' => 'follow_up',
            'notes' => 'نوبت پیگیری خودکار از سابقه پزشکی',
        ]);

        $this->show = false;
        $this->dispatch('notify', message: 'نوبت پیگیری با موفقیت ثبت شد.', type: 'success');
        $this->dispatch('appointment-created');
    }

    public function dismiss(): void
    {
        $this->show = false;
    }

    public function render(): View
    {
        return view('livewire.patient.follow-up-suggestion');
    }
}
