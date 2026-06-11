<?php

namespace App\Http\Controllers;

use App\Models\SmsConfirmation;
use App\Models\Appointment;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SmsWebhookController extends Controller
{
    public function handleConfirmation(Request $request): Response
    {
        $mobile = $request->input('from') ?? $request->input('mobile');
        $message = trim($request->input('message') ?? $request->input('text'));

        // Normalize mobile number
        $mobile = $this->normalizeMobile($mobile);

        // Find pending confirmation for this mobile
        $confirmation = SmsConfirmation::where('mobile', $mobile)
            ->where('status', SmsConfirmation::STATUS_PENDING)
            ->latest()
            ->first();

        if (!$confirmation) {
            return response('NOT_FOUND', 404);
        }

        // Normalize message (convert Persian digits to Western)
        $message = $this->normalizeMessage($message);

        if ($message === '1' || $message === '1.') {
            $confirmation->confirm();
            return response('CONFIRMED');
        }

        if ($message === '2' || $message === '2.') {
            $confirmation->cancelByPatient();
            return response('CANCELLED');
        }

        return response('INVALID_RESPONSE');
    }

    public function sendConfirmationRequest(Appointment $appointment, SmsService $smsService): array
    {
        if (!$appointment->patient || !$appointment->patient->phone) {
            return ['success' => false, 'error' => 'No patient phone'];
        }

        $date = \App\Helpers\JalaliDate::format($appointment->start_at, 'Y/m/d');
        $time = $appointment->start_at->format('H:i');

        $result = $smsService->sendConfirmationRequest(
            $appointment->patient->phone,
            $time,
            $date
        );

        if ($result['success']) {
            SmsConfirmation::createForAppointment($appointment);
        }

        return $result;
    }

    private function normalizeMobile(string $mobile): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $mobile = str_replace($persian, $western, $mobile);
        $mobile = preg_replace('/[^\d]/', '', $mobile);

        return $mobile;
    }

    private function normalizeMessage(string $message): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $message = str_replace($persian, $western, $message);
        $message = trim($message, " \t\n\r\0\x0B.,;");

        return $message;
    }
}
