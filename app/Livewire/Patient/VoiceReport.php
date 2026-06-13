<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use App\Models\MedicalHistory;
use App\Services\VoiceReportService;
use App\Services\FollowUpSuggestionService;
use App\Services\SlotSuggestionService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class VoiceReport extends Component
{
    public Patient $patient;
    public bool $isOpen = false;
    public bool $isRecording = false;
    public string $transcript = '';
    public ?array $structuredReport = null;
    public bool $showFollowUp = false;
    public ?array $followUpSuggestion = null;

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
    }

    public function openModal(): void
    {
        $this->isOpen = true;
        $this->transcript = '';
        $this->structuredReport = null;
        $this->showFollowUp = false;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->isRecording = false;
    }

    public function startRecording(): void
    {
        $this->isRecording = true;
        $this->dispatch('start-voice-recording');
    }

    public function stopRecording(): void
    {
        $this->isRecording = false;
        $this->dispatch('stop-voice-recording');
    }

    public function processTranscript(): void
    {
        $this->validate([
            'transcript' => 'required|string|min:5',
        ], [
            'transcript.required' => 'لطفاً متن گزارش را وارد کنید یا ضبط کنید.',
            'transcript.min' => 'متن گزارش خیلی کوتاه است.',
        ]);

        $service = app(VoiceReportService::class);
        $this->structuredReport = $service->transcribeAndStructure($this->transcript, $this->patient->id);

        // Check for follow-up suggestion
        if ($this->structuredReport['follow_up']) {
            $followUpService = app(FollowUpSuggestionService::class);
            $suggestion = $followUpService->detectFromText($this->structuredReport['follow_up']);
            if ($suggestion) {
                $this->followUpSuggestion = $suggestion;
                $this->showFollowUp = true;
            }
        }
    }

    public function saveReport(): void
    {
        if (!$this->structuredReport) {
            $this->addError('report', 'ابتدا گزارش را پردازش کنید.');
            return;
        }

        $service = app(VoiceReportService::class);
        $history = $service->createMedicalReport($this->structuredReport, auth()->id());

        // If follow-up suggested, create appointment
        if ($this->showFollowUp && $this->followUpSuggestion) {
            $slotService = app(SlotSuggestionService::class);
            $firstSlot = $slotService->findFirstAvailableSlot(
                \Carbon\Carbon::parse($this->followUpSuggestion['date'])
            );

            if ($firstSlot) {
                $startAt = \Carbon\Carbon::parse($firstSlot['datetime']);
                \App\Models\Appointment::create([
                    'patient_id' => $this->patient->id,
                    'created_by' => auth()->id(),
                    'title' => 'نوبت پیگیری',
                    'start_at' => $startAt,
                    'end_at' => $startAt->copy()->addMinutes(30),
                    'status' => \App\Models\Appointment::STATUS_RESERVED,
                    'type' => 'follow_up',
                    'notes' => 'ثبت خودکار از گزارش صوتی',
                ]);
            }

            // Update follow_up_date on the history
            $history->update(['follow_up_date' => $this->followUpSuggestion['date']]);
        }

        $this->dispatch('notify', message: 'گزارش پزشکی از یادداشت صوتی ثبت شد.', type: 'success');
        $this->dispatch('history-updated');
        $this->closeModal();
    }

    public function render(): View
    {
        return view('livewire.patient.voice-report');
    }
}
