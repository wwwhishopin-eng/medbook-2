<?php

namespace App\Services;

use App\Helpers\JalaliDate;
use App\Models\Appointment;
use App\Models\MedicalHistory;
use Carbon\Carbon;

class FollowUpSuggestionService
{
    private const PATTERNS = [
        '/(\d+)\s*هفته\s*دیگر/' => 'weeks',
        '/(\d+)\s*روز\s*دیگر/' => 'days',
        '/(\d+)\s*ماه\s*دیگر/' => 'months',
        '/هفته\s*آینده/' => 'next_week',
        '/فردا/' => 'tomorrow',
        '/پس[\s\-]*فردا/' => 'day_after_tomorrow',
        '/ماه\s*آینده/' => 'next_month',
    ];

    public function detectFromText(string $text): ?array
    {
        foreach (self::PATTERNS as $pattern => $type) {
            if (preg_match($pattern, $text, $matches)) {
                return $this->computeDate($type, isset($matches[1]) ? (int)$matches[1] : 1);
            }
        }

        return null;
    }

    public function detectFromMedicalHistory(MedicalHistory $history): ?array
    {
        $text = trim(
            ($history->doctor_notes ?? '') . ' ' .
            ($history->treatment ?? '') . ' ' .
            ($history->prescriptions ?? '')
        );

        return $this->detectFromText($text);
    }

    private function computeDate(string $type, int $value): ?array
    {
        $date = match ($type) {
            'weeks' => now()->addWeeks($value),
            'days' => now()->addDays($value),
            'months' => now()->addMonths($value),
            'next_week' => now()->addWeek(),
            'tomorrow' => now()->addDay(),
            'day_after_tomorrow' => now()->addDays(2),
            'next_month' => now()->addMonth(),
            default => null,
        };

        if (!$date) {
            return null;
        }

        return [
            'date' => $date->format('Y-m-d'),
            'date_jalali' => JalaliDate::format($date, 'Y/m/d'),
            'day_name' => JalaliDate::format($date, 'l'),
            'label' => $this->formatLabel($type, $value),
            'default_time' => '09:00',
        ];
    }

    private function formatLabel(string $type, int $value): string
    {
        return match ($type) {
            'weeks' => "{$value} هفته دیگر",
            'days' => "{$value} روز دیگر",
            'months' => "{$value} ماه دیگر",
            'next_week' => 'هفته آینده',
            'tomorrow' => 'فردا',
            'day_after_tomorrow' => 'پس‌فردا',
            'next_month' => 'ماه آینده',
            default => '',
        };
    }

    public function suggestFollowUpAppointment(MedicalHistory $history, SlotSuggestionService $slotService): ?array
    {
        $suggestion = $this->detectFromMedicalHistory($history);

        if (!$suggestion) {
            return null;
        }

        $slots = $slotService->getAvailableSlotsForDay(Carbon::parse($suggestion['date']));

        if (empty($slots)) {
            $firstSlot = $slotService->findFirstAvailableSlot(Carbon::parse($suggestion['date']));
            if ($firstSlot) {
                $suggestion['available_slot'] = $firstSlot;
                $suggestion['alternative'] = true;
            }
            return $suggestion;
        }

        $suggestion['available_slot'] = $slots[0];
        $suggestion['alternative'] = false;

        return $suggestion;
    }
}
