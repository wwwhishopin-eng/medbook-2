<?php

namespace App\Services;

use App\Models\Appointment;
use App\Helpers\JalaliDate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminderMail;

class ReminderService
{
    private SmsService $smsService;
    private VoiceCallService $voiceService;

    public function __construct(SmsService $smsService, VoiceCallService $voiceService)
    {
        $this->smsService = $smsService;
        $this->voiceService = $voiceService;
    }

    public function processReminders(): void
    {
        $this->process24HourReminders();
        $this->process2HourReminders();
        $this->process30MinuteReminders();
    }

    public function process24HourReminders(): void
    {
        $appointments = Appointment::query()
            ->where('start_at', '>=', now())
            ->where('start_at', '<=', now()->addHours(24))
            ->whereIn('status', [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED])
            ->where('reminder_sent', false)
            ->with('patient')
            ->get();

        foreach ($appointments as $appointment) {
            $this->send24HourReminder($appointment);
        }
    }

    public function process2HourReminders(): void
    {
        $appointments = Appointment::query()
            ->where('start_at', '>=', now())
            ->where('start_at', '<=', now()->addHours(2))
            ->whereIn('status', [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED])
            ->with('patient')
            ->get();

        foreach ($appointments as $appointment) {
            $this->send2HourReminder($appointment);
        }
    }

    public function process30MinuteReminders(): void
    {
        $appointments = Appointment::query()
            ->where('start_at', '>=', now())
            ->where('start_at', '<=', now()->addMinutes(30))
            ->whereIn('status', [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED])
            ->with('patient')
            ->get();

        foreach ($appointments as $appointment) {
            $this->send30MinuteReminder($appointment);
        }
    }

    public function send24HourReminder(Appointment $appointment): bool
    {
        if (!$appointment->patient || !$appointment->patient->phone) {
            return false;
        }

        $date = JalaliDate::format($appointment->start_at, 'Y/m/d');
        $time = $appointment->start_at->format('H:i');
        $patientName = $appointment->patient->full_name;

        $result = $this->smsService->sendConfirmationRequest(
            $appointment->patient->phone,
            $time,
            $date
        );

        if ($result['success']) {
            Log::info('24-hour reminder sent', ['appointment_id' => $appointment->id]);
            $appointment->reminder_sent = true;
            $appointment->saveQuietly();
            return true;
        }

        Log::error('Failed to send 24-hour reminder', [
            'appointment_id' => $appointment->id,
            'error' => $result['error'] ?? 'Unknown error'
        ]);

        return false;
    }

    public function send2HourReminder(Appointment $appointment): bool
    {
        if (!$appointment->patient || !$appointment->patient->phone) {
            return false;
        }

        $date = JalaliDate::format($appointment->start_at, 'Y/m/d');
        $time = $appointment->start_at->format('H:i');
        $patientName = $appointment->patient->full_name;

        $result = $this->smsService->sendAppointmentReminder(
            $appointment->patient->phone,
            $patientName,
            $date,
            $time
        );

        if ($result['success']) {
            Log::info('2-hour reminder sent', ['appointment_id' => $appointment->id]);
            return true;
        }

        Log::error('Failed to send 2-hour reminder', [
            'appointment_id' => $appointment->id,
            'error' => $result['error'] ?? 'Unknown error'
        ]);

        return false;
    }

    public function send30MinuteReminder(Appointment $appointment): bool
    {
        if (!$appointment->patient || !$appointment->patient->phone) {
            return false;
        }

        $time = $appointment->start_at->format('H:i');
        $patientName = $appointment->patient->full_name;

        if ($this->voiceService->isConfigured()) {
            $result = $this->voiceService->sendAppointmentReminder(
                $appointment->patient->phone,
                $patientName,
                $time
            );

            if ($result['success']) {
                Log::info('30-minute voice reminder sent', ['appointment_id' => $appointment->id]);
                return true;
            }

            Log::error('Failed to send voice reminder', [
                'appointment_id' => $appointment->id,
                'error' => $result['error'] ?? 'Unknown error'
            ]);
        }

        return false;
    }
}
