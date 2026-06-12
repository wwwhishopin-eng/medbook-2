<?php

namespace App\Livewire\Queue;

use App\Helpers\JalaliDate;
use App\Models\Appointment;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class WaitingRoomDisplay extends Component
{
    public function render(): View
    {
        $today = now();

        $currentPatient = Appointment::query()
            ->with('patient')
            ->whereDate('start_at', $today)
            ->where('status', Appointment::STATUS_ARRIVED)
            ->orderBy('start_at')
            ->first();

        $waitingCount = Appointment::query()
            ->whereDate('start_at', $today)
            ->whereIn('status', [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED, Appointment::STATUS_ARRIVED])
            ->where('start_at', '>=', now())
            ->count();

        $upcoming = Appointment::query()
            ->with('patient')
            ->whereDate('start_at', $today)
            ->whereIn('status', [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED])
            ->where('start_at', '>=', now())
            ->orderBy('start_at')
            ->limit(5)
            ->get();

        $completedCount = Appointment::query()
            ->whereDate('start_at', $today)
            ->where('status', Appointment::STATUS_COMPLETED)
            ->count();

        return view('livewire.queue.waiting-room-display', [
            'currentPatient' => $currentPatient,
            'waitingCount' => $waitingCount,
            'upcoming' => $upcoming,
            'completedCount' => $completedCount,
            'dateJalali' => JalaliDate::format($today, 'l Y/m/d'),
            'time' => $today->format('H:i'),
        ]);
    }
}
