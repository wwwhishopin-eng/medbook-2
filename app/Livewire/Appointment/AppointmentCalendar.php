<?php

namespace App\Livewire\Appointment;

use App\Helpers\JalaliDate;
use App\Models\Appointment;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AppointmentCalendar extends Component
{
    public string $view = 'daily';
    public string $selectedDate = '';
    public string $statusFilter = '';

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function setView(string $view): void
    {
        $this->view = $view;
    }

    public function previousPeriod(): void
    {
        $date = \Carbon\Carbon::parse($this->selectedDate);
        if ($this->view === 'daily') {
            $date->subDay();
        } elseif ($this->view === 'weekly') {
            $date->subWeek();
        } else {
            $date->subMonth();
        }
        $this->selectedDate = $date->format('Y-m-d');
    }

    public function nextPeriod(): void
    {
        $date = \Carbon\Carbon::parse($this->selectedDate);
        if ($this->view === 'daily') {
            $date->addDay();
        } elseif ($this->view === 'weekly') {
            $date->addWeek();
        } else {
            $date->addMonth();
        }
        $this->selectedDate = $date->format('Y-m-d');
    }

    public function goToday(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        $date = \Carbon\Carbon::parse($this->selectedDate);

        $query = Appointment::query()->with('patient');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->view === 'daily') {
            $appointments = $query->whereDate('start_at', $date)
                ->orderBy('start_at')
                ->get();
            $hours = collect(range(8, 20))->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00');
        } elseif ($this->view === 'weekly') {
            $startOfWeek = $date->copy()->startOfWeek();
            $endOfWeek = $date->copy()->endOfWeek();
            $appointments = $query->whereBetween('start_at', [$startOfWeek, $endOfWeek])
                ->orderBy('start_at')
                ->get();

            $days = collect();
            for ($i = 0; $i < 7; $i++) {
                $day = $startOfWeek->copy()->addDays($i);
                $days->push([
                    'date' => $day->format('Y-m-d'),
                    'jalali' => JalaliDate::format($day, 'Y/m/d'),
                    'day_name' => JalaliDate::format($day, 'l'),
                    'is_today' => $day->isToday(),
                ]);
            }
            $hours = collect(range(8, 20))->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00');
        } else {
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            $appointments = $query->whereBetween('start_at', [$startOfMonth, $endOfMonth])
                ->orderBy('start_at')
                ->get();

            $days = collect();
            $current = $startOfWeek = $startOfMonth->copy()->startOfWeek();
            $endOfView = $endOfMonth->copy()->endOfWeek();
            while ($current->lte($endOfView)) {
                $days->push([
                    'date' => $current->format('Y-m-d'),
                    'jalali' => JalaliDate::format($current, 'd'),
                    'jalali_full' => JalaliDate::format($current, 'Y/m/d'),
                    'day_name' => JalaliDate::format($current, 'D'),
                    'is_current_month' => $current->between($startOfMonth, $endOfMonth),
                    'is_today' => $current->isToday(),
                ]);
                $current->addDay();
            }
        }

        $dateLabel = match($this->view) {
            'daily' => JalaliDate::format($date, 'l Y/m/d'),
            'weekly' => JalaliDate::format($date->copy()->startOfWeek(), 'Y/m/d') . ' - ' . JalaliDate::format($date->copy()->endOfWeek(), 'Y/m/d'),
            'monthly' => JalaliDate::format($date, 'F Y'),
            default => '',
        };

        return view('livewire.appointment.appointment-calendar', [
            'appointments' => $appointments,
            'hours' => $hours ?? collect(),
            'days' => $days ?? collect(),
            'dateLabel' => $dateLabel,
        ]);
    }
}
