<?php

namespace App\Services\SMS;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KavenegarSmsProvider implements SmsProviderInterface
{
    private string $apiKey;
    private string $sender;
    private string $baseUrl = 'https://api.kavenegar.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.kavenegar.api_key', env('KAVENEGAR_API_KEY'));
        $this->sender = config('services.kavenegar.sender', env('KAVENEGAR_SENDER'));
    }

    public function send(string $mobile, string $message): array
    {
        if (!$this->isConfigured()) {
            Log::warning('Kavenegar SMS: Provider not configured');
            return [
                'success' => false,
                'error' => 'Provider not configured',
                'provider' => 'kavenegar'
            ];
        }

        // Convert Persian numbers to Western for API
        $mobile = $this->normalizeMobile($mobile);

        try {
            $response = Http::get("{$this->baseUrl}/{$this->apiKey}/sms/send.json", [
                'receptor' => $mobile,
                'sender' => $this->sender,
                'message' => $message,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['return']['status']) && $data['return']['status'] == 200) {
                Log::info('Kavenegar SMS sent successfully', ['mobile' => $mobile]);
                return [
                    'success' => true,
                    'message_id' => $data['entries'][0]['messageid'] ?? null,
                    'provider' => 'kavenegar'
                ];
            }

            Log::error('Kavenegar SMS failed', ['response' => $data]);
            return [
                'success' => false,
                'error' => $data['return']['message'] ?? 'Unknown error',
                'provider' => 'kavenegar'
            ];
        } catch (\Exception $e) {
            Log::error('Kavenegar SMS exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'kavenegar'
            ];
        }
    }

    public function sendBulk(array $recipients, string $message): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Provider not configured'];
        }

        $recipients = array_map([$this, 'normalizeMobile'], $recipients);

        try {
            $response = Http::get("{$this->baseUrl}/{$this->apiKey}/sms/send.json", [
                'receptor' => implode(',', $recipients),
                'sender' => $this->sender,
                'message' => $message,
            ]);

            return [
                'success' => $response->successful(),
                'response' => $response->json(),
                'provider' => 'kavenegar'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getProviderName(): string
    {
        return 'kavenegar';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->sender);
    }

    private function normalizeMobile(string $mobile): string
    {
        // Convert Persian digits to Western
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $mobile = str_replace($persian, $western, $mobile);

        // Remove non-digits
        $mobile = preg_replace('/[^\d]/', '', $mobile);

        // Add country code if needed
        if (str_starts_with($mobile, '0')) {
            $mobile = '98' . substr($mobile, 1);
        } elseif (!str_starts_with($mobile, '98')) {
            $mobile = '98' . $mobile;
        }

        return $mobile;
    }
}
