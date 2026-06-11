<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlotSuggestionService
{
    private int $defaultSlotMinutes = 30;
    private int $workStartHour = 8;
    private int $workEndHour = 20;

    public function getSuggestions(?string $date = null, int $count = 3): array
    {
        $targetDate = $date ? Carbon::parse($date) : now();

        if ($targetDate->isPast() && !$targetDate->isToday()) {
            $targetDate = now();
        }

        // Find first available slot
        $firstAvailable = $this->findFirstAvailableSlot($targetDate);

        // Find alternative slots
        $alternatives = $this->findAlternativeSlots($targetDate, $count, $firstAvailable);

        return [
            'first_available' => $firstAvailable,
            'alternatives' => $alternatives,
            'date' => $targetDate->format('Y-m-d'),
        ];
    }

    public function findFirstAvailableSlot(Carbon $date): ?array
    {
        $start = $date->copy()->setTime($this->workStartHour, 0);
        $end = $date->copy()->setTime($this->workEndHour, 0);

        $existingAppointments = Appointment::query()
            ->whereDate('start_at', $date)
            ->whereIn('status', [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED])
            ->orderBy('start_at')
            ->get(['start_at', 'end_at']);

        while ($start->lt($end)) {
            $slotEnd = $start->copy()->addMinutes($this->defaultSlotMinutes);

            $isAvailable = !$existingAppointments->contains(function ($appt) use ($start, $slotEnd) {
                return $start->lt($appt->end_at) && $slotEnd->gt($appt->start_at);
            });

            if ($isAvailable) {
                return [
                    'start' => $start->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'date' => $date->format('Y-m-d'),
                    'date_jalali' => \App\Helpers\JalaliDate::format($date, 'Y/m/d'),
                    'datetime' => $start->format('Y-m-d H:i:s'),
                    'position' => $this->getPositionInDay($start),
                ];
            }

            $start->addMinutes($this->defaultSlotMinutes);
        }

        // No slot available today, check tomorrow
        $nextDay = $date->copy()->addDay();
        while ($nextDay->isWeekend()) {
            $nextDay->addDay();
        }

        return $this->findFirstAvailableSlot($nextDay);
    }

    public function findAlternativeSlots(Carbon $date, int $count = 3, ?array $excludeFirst = null): array
    {
        $alternatives = [];
        $searchDate = $date->copy();

        for ($day = 0; $day < 14; $day++) {
            if ($day > 0) {
                $searchDate = $date->copy()->addDays($day);
                // Skip weekends if needed (optional)
                // if ($searchDate->isWeekend()) continue;
            }

            $slots = $this->getAvailableSlotsForDay($searchDate);

            foreach ($slots as $slot) {
                // Skip if this is the first available we already found
                if ($excludeFirst && $slot['datetime'] === $excludeFirst['datetime']) {
                    continue;
                }

                $alternatives[] = $slot;

                if (count($alternatives) >= $count) {
                    return $alternatives;
                }
            }
        }

        return $alternatives;
    }

    public function getAvailableSlotsForDay(Carbon $date): array
    {
        $start = $date->copy()->setTime($this->workStartHour, 0);
        $end = $date->copy()->setTime($this->workEndHour, 0);

        $existingAppointments = Appointment::query()
            ->whereDate('start_at', $date)
            ->whereIn('status', [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED])
            ->orderBy('start_at')
            ->get(['start_at', 'end_at']);

        $slots = [];

        while ($start->lt($end)) {
            $slotEnd = $start->copy()->addMinutes($this->defaultSlotMinutes);

            $isAvailable = !$existingAppointments->contains(function ($appt) use ($start, $slotEnd) {
                return $start->lt($appt->end_at) && $slotEnd->gt($appt->start_at);
            });

            if ($isAvailable) {
                $slots[] = [
                    'start' => $start->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'date' => $date->format('Y-m-d'),
                    'date_jalali' => \App\Helpers\JalaliDate::format($date, 'Y/m/d'),
                    'datetime' => $start->format('Y-m-d H:i:s'),
                    'position' => $this->getPositionInDay($start),
                ];
            }

            $start->addMinutes($this->defaultSlotMinutes);
        }

        return $slots;
    }

    private function getPositionInDay(Carbon $time): string
    {
        $hour = $time->hour;

        if ($hour < 10) {
            return 'صبح اول';
        } elseif ($hour < 12) {
            return 'صبح';
        } elseif ($hour < 14) {
            return 'ظهر';
        } elseif ($hour < 17) {
            return 'بعد از ظهر';
        } else {
            return 'عصر';
        }
    }

    public function checkSlotAvailability(string $datetime): bool
    {
        $start = Carbon::parse($datetime);
        $end = $start->copy()->addMinutes($this->defaultSlotMinutes);

        $conflict = Appointment::query()
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start)
            ->whereIn('status', [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED])
            ->exists();

        return !$conflict;
    }

    public function getAvailableSlotsForDateRange(Carbon $from, Carbon $to): array
    {
        $slots = [];
        $current = $from->copy();

        while ($current->lte($to)) {
            $daySlots = $this->getAvailableSlotsForDay($current);
            foreach ($daySlots as $slot) {
                $slots[] = $slot;
            }
            $current->addDay();
        }

        return $slots;
    }
}
