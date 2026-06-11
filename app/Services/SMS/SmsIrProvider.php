<?php

namespace App\Services\SMS;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsIrProvider implements SmsProviderInterface
{
    private string $apiKey;
    private string $lineNumber;
    private string $baseUrl = 'https://RestfulSms.com/api';

    public function __construct()
    {
        $this->apiKey = config('services.smsir.api_key', env('SMSIR_API_KEY'));
        $this->lineNumber = config('services.smsir.line_number', env('SMSIR_LINE_NUMBER'));
    }

    public function send(string $mobile, string $message): array
    {
        if (!$this->isConfigured()) {
            Log::warning('SMS.ir: Provider not configured');
            return [
                'success' => false,
                'error' => 'Provider not configured',
                'provider' => 'smsir'
            ];
        }

        $mobile = $this->normalizeMobile($mobile);
        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to get API token',
                'provider' => 'smsir'
            ];
        }

        try {
            $response = Http::withHeaders([
                'x-sms-ir-secure-token' => $token,
            ])->post("{$this->baseUrl}/MessageSend", [
                'Messages' => [$message],
                'MobileNumbers' => [$mobile],
                'LineNumber' => $this->lineNumber,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['IsSuccessful']) && $data['IsSuccessful']) {
                Log::info('SMS.ir sent successfully', ['mobile' => $mobile]);
                return [
                    'success' => true,
                    'message_id' => $data['Ids'][0] ?? null,
                    'provider' => 'smsir'
                ];
            }

            Log::error('SMS.ir failed', ['response' => $data]);
            return [
                'success' => false,
                'error' => $data['Message'] ?? 'Unknown error',
                'provider' => 'smsir'
            ];
        } catch (\Exception $e) {
            Log::error('SMS.ir exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'smsir'
            ];
        }
    }

    public function sendBulk(array $recipients, string $message): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Provider not configured'];
        }

        $recipients = array_map([$this, 'normalizeMobile'], $recipients);
        $token = $this->getToken();

        if (!$token) {
            return ['success' => false, 'error' => 'Failed to get API token'];
        }

        try {
            $response = Http::withHeaders([
                'x-sms-ir-secure-token' => $token,
            ])->post("{$this->baseUrl}/MessageSend", [
                'Messages' => [$message],
                'MobileNumbers' => $recipients,
                'LineNumber' => $this->lineNumber,
            ]);

            $data = $response->json();
            return [
                'success' => $response->successful() && ($data['IsSuccessful'] ?? false),
                'response' => $data,
                'provider' => 'smsir'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getProviderName(): string
    {
        return 'smsir';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->lineNumber);
    }

    private function getToken(): ?string
    {
        try {
            $userApiKey = config('services.smsir.user_key', env('SMSIR_USER_KEY'));
            $secretKey = config('services.smsir.secret_key', env('SMSIR_SECRET_KEY'));

            $response = Http::post("{$this->baseUrl}/Token", [
                'UserApiKey' => $userApiKey,
                'SecretKey' => $secretKey,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['TokenKey'])) {
                return $data['TokenKey'];
            }
        } catch (\Exception $e) {
            Log::error('SMS.ir token fetch failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    private function normalizeMobile(string $mobile): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $mobile = str_replace($persian, $western, $mobile);
        $mobile = preg_replace('/[^\d]/', '', $mobile);
        if (str_starts_with($mobile, '0')) {
            $mobile = substr($mobile, 1);
        }
        return '0' . $mobile;
    }
}
