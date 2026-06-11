<?php

namespace App\Livewire\Appointment;

use App\Helpers\Persian;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\WaitingList;
use App\Services\SlotSuggestionService;
use App\Services\SMS\SmsService;
use App\Models\SmsConfirmation;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class QuickBooking extends Component
{
    public string $searchPhone = '';
    public ?Patient $patient = null;
    public bool $showNewPatientForm = false;

    // New patient fields
    public string $new_first_name = '';
    public string $new_last_name = '';
    public string $new_phone = '';
    public string $new_national_id = '';
    public string $new_gender = 'male';

    // Appointment fields
    public string $selected_slot = '';
    public string $selected_date = '';
    public string $appointment_type = 'checkup';
    public string $appointment_notes = '';

    // Slot suggestions
    public array $suggestedSlots = [];
    public ?array $firstAvailable = null;

    protected SlotSuggestionService $slotService;

    public function boot(SlotSuggestionService $slotService): void
    {
        $this->slotService = $slotService;
    }

    public function mount(): void
    {
        $this->loadSuggestions();
    }

    public function updatedSearchPhone(): void
    {
        $searchPhone = Persian::toWestern($this->searchPhone);
        $searchPhone = preg_replace('/[^\d]/', '', $searchPhone);

        if (strlen($searchPhone) >= 10) {
            $this->patient = Patient::where('phone', 'like', "%{$searchPhone}%")->first();

            if ($this->patient) {
                $this->showNewPatientForm = false;
                $this->dispatch('patient-found', patientId: $this->patient->id);
            } else {
                $this->showNewPatientForm = true;
                $this->new_phone = $searchPhone;
            }
        } else {
            $this->patient = null;
            $this->showNewPatientForm = false;
        }
    }

    public function selectSlot(string $datetime): void
    {
        $this->selected_slot = $datetime;
        $this->selected_date = \Carbon\Carbon::parse($datetime)->format('Y-m-d');
    }

    public function loadSuggestions(): void
    {
        $suggestions = $this->slotService->getSuggestions(now()->format('Y-m-d'), 4);
        $this->firstAvailable = $suggestions['first_available'];
        $this->suggestedSlots = array_merge(
            $suggestions['first_available'] ? [$suggestions['first_available']] : [],
            $suggestions['alternatives']
        );
    }

    public function createQuickAppointment(SmsService $smsService): void
    {
        $this->validate([
            'selected_slot' => 'required',
        ], [
            'selected_slot.required' => 'لطفاً ساعت نوبت را انتخاب کنید.',
        ]);

        // If no patient, create one
        if (!$this->patient && $this->showNewPatientForm) {
            $this->validate([
                'new_first_name' => 'required|string|max:60',
                'new_last_name' => 'required|string|max:60',
                'new_phone' => 'required|string|max:20',
            ], [
                'new_first_name.required' => 'نام الزامی است.',
                'new_last_name.required' => 'نام خانوادگی الزامی است.',
                'new_phone.required' => 'شماره موبایل الزامی است.',
            ]);

            $this->patient = Patient::create([
                'first_name' => $this->new_first_name,
                'last_name' => $this->new_last_name,
                'phone' => $this->new_phone,
                'national_id' => $this->new_national_id ?: null,
                'gender' => $this->new_gender,
                'status' => 'active',
                'avatar_color' => Patient::randomAvatarColor(),
            ]);
        }

        if (!$this->patient) {
            $this->addError('patient', 'لطفاً ابتدا بیمار را مشخص کنید.');
            return;
        }

        // Check for debt warning
        $debtWarning = null;
        // In future: implement debt tracking

        // Create appointment
        $startAt = \Carbon\Carbon::parse($this->selected_slot);
        $endAt = $startAt->copy()->addMinutes(30);

        $appointment = Appointment::create([
            'patient_id' => $this->patient->id,
            'created_by' => auth()->id(),
            'title' => $this->appointment_type === 'checkup' ? 'ویزیت عمومی' : 'نوبت',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => Appointment::STATUS_RESERVED,
            'type' => $this->appointment_type,
            'notes' => $this->appointment_notes,
        ]);

        // Send SMS confirmation request
        if ($this->patient->phone) {
            $date = \App\Helpers\JalaliDate::format($startAt, 'Y/m/d');
            $time = $startAt->format('H:i');
            $smsService->sendConfirmationRequest($this->patient->phone, $time, $date);

            SmsConfirmation::createForAppointment($appointment);
        }

        $this->dispatch('appointment-created', appointmentId: $appointment->id);
        $this->dispatch('notify',
            message: "نوبت برای {$this->patient->full_name} در تاریخ " . \App\Helpers\JalaliDate::format($startAt, 'Y/m/d H:i') . " ثبت شد.",
            type: 'success'
        );

        $this->resetForm();
        $this->loadSuggestions();
    }

    public function addToWaitingList(): void
    {
        if (!$this->patient && $this->showNewPatientForm) {
            $this->validate([
                'new_first_name' => 'required|string|max:60',
                'new_last_name' => 'required|string|max:60',
                'new_phone' => 'required|string|max:20',
            ]);

            $this->patient = Patient::create([
                'first_name' => $this->new_first_name,
                'last_name' => $this->new_last_name,
                'phone' => $this->new_phone,
                'national_id' => $this->new_national_id ?: null,
                'gender' => $this->new_gender,
                'status' => 'active',
                'avatar_color' => Patient::randomAvatarColor(),
            ]);
        }

        if (!$this->patient) {
            $this->addError('patient', 'لطفاً ابتدا بیمار را مشخص کنید.');
            return;
        }

        WaitingList::create([
            'patient_id' => $this->patient->id,
            'status' => WaitingList::STATUS_WAITING,
            'notes' => 'قرار گرفتن در لیست انتظار از طریق ثبت سریع',
            'created_by' => auth()->id(),
        ]);

        $this->dispatch('notify',
            message: "بیمار {$this->patient->full_name} به لیست انتظار اضافه شد.",
            type: 'success'
        );

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->searchPhone = '';
        $this->patient = null;
        $this->showNewPatientForm = false;
        $this->new_first_name = '';
        $this->new_last_name = '';
        $this->new_phone = '';
        $this->new_national_id = '';
        $this->new_gender = 'male';
        $this->selected_slot = '';
        $this->selected_date = '';
        $this->appointment_type = 'checkup';
        $this->appointment_notes = '';
    }

    public function render(): View
    {
        return view('livewire.appointment.quick-booking');
    }
}
