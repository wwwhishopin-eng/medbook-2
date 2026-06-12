<?php

namespace App\Livewire\Queue;

use App\Helpers\JalaliDate;
use App\Models\Appointment;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class QueueManager extends Component
{
    public string $selectedDate = '';

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function markAsArrived(int $appointmentId): void
    {
        $appointment = Appointment::find($appointmentId);
        if ($appointment && in_array($appointment->status, [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED])) {
            $appointment->status = Appointment::STATUS_ARRIVED;
            $appointment->save();

            $this->dispatch('notify', message: "بیمار {$appointment->patient->full_name} در مطب حاضر ثبت شد.", type: 'success');
        }
    }

    public function markAsCompleted(int $appointmentId): void
    {
        $appointment = Appointment::find($appointmentId);
        if ($appointment && $appointment->status === Appointment::STATUS_ARRIVED) {
            $appointment->status = Appointment::STATUS_COMPLETED;
            $appointment->save();

            $this->dispatch('notify', message: "ویزیت بیمار {$appointment->patient->full_name} تکمیل شد.", type: 'success');
        }
    }

    public function markAsNoShow(int $appointmentId): void
    {
        $appointment = Appointment::find($appointmentId);
        if ($appointment && in_array($appointment->status, [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED, Appointment::STATUS_ARRIVED])) {
            $appointment->status = Appointment::STATUS_NO_SHOW;
            $appointment->save();

            $this->dispatch('notify', message: "بیمار {$appointment->patient->full_name} غایب ثبت شد.", type: 'success');
        }
    }

    public function callNext(): void
    {
        $next = Appointment::query()
            ->whereDate('start_at', $this->selectedDate)
            ->where('status', Appointment::STATUS_ARRIVED)
            ->orderBy('start_at')
            ->first();

        if ($next) {
            $this->dispatch('notify', message: "نفر بعدی: {$next->patient->full_name}", type: 'success');
        } else {
            $this->dispatch('notify', message: "بیمار حاضری در صف نیست.", type: 'info');
        }
    }

    public function render(): View
    {
        $date = \Carbon\Carbon::parse($this->selectedDate);

        $appointments = Appointment::query()
            ->with('patient')
            ->whereDate('start_at', $date)
            ->whereIn('status', [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED, Appointment::STATUS_ARRIVED, Appointment::STATUS_COMPLETED])
            ->orderBy('start_at')
            ->get();

        $arrived = $appointments->filter(fn($a) => $a->status === Appointment::STATUS_ARRIVED);
        $completed = $appointments->filter(fn($a) => $a->status === Appointment::STATUS_COMPLETED);
        $waiting = $appointments->filter(fn($a) => in_array($a->status, [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED]));

        $currentPatient = $arrived->sortBy('start_at')->first();

        return view('livewire.queue.queue-manager', [
            'appointments' => $appointments,
            'arrived' => $arrived,
            'completed' => $completed,
            'waiting' => $waiting,
            'currentPatient' => $currentPatient,
            'dateJalali' => JalaliDate::format($date, 'Y/m/d'),
        ]);
    }
}
