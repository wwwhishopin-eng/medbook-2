<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VoiceCallService
{
    private string $apiKey;
    private string $template;
    private string $baseUrl = 'https://api.kavenegar.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.kavenegar.api_key', env('KAVENEGAR_API_KEY'));
        $this->template = config('services.kavenegar.voice_template', env('KAVENEGAR_VOICE_TEMPLATE', 'appointment_reminder'));
    }

    public function call(string $mobile, string $message): array
    {
        if (!$this->isConfigured()) {
            Log::warning('Voice Call: Provider not configured');
            return [
                'success' => false,
                'error' => 'Voice call provider not configured'
            ];
        }

        $mobile = $this->normalizeMobile($mobile);

        try {
            $response = Http::get("{$this->baseUrl}/{$this->apiKey}/call/maketts.json", [
                'receptor' => $mobile,
                'message' => $message,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['return']['status']) && $data['return']['status'] == 200) {
                Log::info('Voice call initiated successfully', ['mobile' => $mobile]);
                return [
                    'success' => true,
                    'call_id' => $data['entries'][0]['id'] ?? null,
                    'status' => 'initiated'
                ];
            }

            Log::error('Voice call failed', ['response' => $data]);
            return [
                'success' => false,
                'error' => $data['return']['message'] ?? 'Unknown error'
            ];
        } catch (\Exception $e) {
            Log::error('Voice call exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendAppointmentReminder(string $mobile, string $patientName, string $time, ?string $date = null): array
    {
        $dateStr = $date ?? \App\Helpers\JalaliDate::now();
        $timeFormatted = $this->formatTimeForSpeech($time);

        $message = "بیمار گرامی {$patientName}، ";
        $message .= "یادآوری می‌شود که نوبت شما امروز ساعت {$timeFormatted} می‌باشد. ";
        $message .= "لطفاً در زمان مقرر در مطب حضور داشته باشید.";

        return $this->call($mobile, $message);
    }

    public function sendCustomMessage(string $mobile, string $message): array
    {
        return $this->call($mobile, $message);
    }

    private function formatTimeForSpeech(string $time): string
    {
        $parts = explode(':', $time);
        $hour = (int)$parts[0];
        $minute = isset($parts[1]) ? (int)$parts[1] : 0;

        $period = $hour >= 12 ? 'بعد از ظهر' : 'قبل از ظهر';
        if ($hour > 12) $hour -= 12;
        if ($hour === 0) $hour = 12;

        $persianHour = $this->numberToPersianWords($hour);
        $persianMinute = $minute > 0 ? 'و ' . $this->numberToPersianWords($minute) . ' دقیقه' : '';

        return "{$persianHour} {$persianMinute} {$period}";
    }

    private function numberToPersianWords(int $num): string
    {
        $ones = [
            1 => 'یک', 2 => 'دو', 3 => 'سه', 4 => 'چهار', 5 => 'پنج',
            6 => 'شش', 7 => 'هفت', 8 => 'هشت', 9 => 'نه', 10 => 'ده',
            11 => 'یازده', 12 => 'دوازده', 13 => 'سیزده', 14 => 'چهارده', 15 => 'پانزده',
            16 => 'شانزده', 17 => 'هفده', 18 => 'هجده', 19 => 'نوزده'
        ];

        $tens = [
            2 => 'بیست', 3 => 'سی', 4 => 'چهل', 5 => 'پنجاه',
            6 => 'شصت', 7 => 'هفتاد', 8 => 'هشتاد', 9 => 'نود'
        ];

        if ($num <= 19) {
            return $ones[$num] ?? (string)$num;
        }

        if ($num < 100) {
            $ten = (int)($num / 10);
            $one = $num % 10;
            $word = $tens[$ten];
            if ($one > 0) {
                $word .= ' و ' . $ones[$one];
            }
            return $word;
        }

        return (string)$num;
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    private function normalizeMobile(string $mobile): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $mobile = str_replace($persian, $western, $mobile);
        $mobile = preg_replace('/[^\d]/', '', $mobile);

        if (str_starts_with($mobile, '0')) {
            $mobile = '98' . substr($mobile, 1);
        } elseif (!str_starts_with($mobile, '98')) {
            $mobile = '98' . $mobile;
        }

        return $mobile;
    }
}
