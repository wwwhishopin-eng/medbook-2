<?php

namespace App\Services\SMS;

use Illuminate\Support\Facades\Log;

class SmsService
{
    private ?SmsProviderInterface $provider;

    public function __construct()
    {
        $this->provider = $this->resolveProvider();
    }

    private function resolveProvider(): ?SmsProviderInterface
    {
        $providerName = config('services.sms.provider', env('SMS_PROVIDER', 'kavenegar'));

        return match ($providerName) {
            'kavenegar' => new KavenegarSmsProvider(),
            'smsir' => new SmsIrProvider(),
            default => null,
        };
    }

    public function send(string $mobile, string $message): array
    {
        if (!$this->provider) {
            Log::error('SMS: No provider configured');
            return [
                'success' => false,
                'error' => 'SMS provider not configured'
            ];
        }

        return $this->provider->send($mobile, $message);
    }

    public function sendBulk(array $recipients, string $message): array
    {
        if (!$this->provider) {
            return ['success' => false, 'error' => 'SMS provider not configured'];
        }

        return $this->provider->sendBulk($recipients, $message);
    }

    public function sendAppointmentReminder(string $mobile, string $patientName, string $date, string $time): array
    {
        $message = "بیمار گرامی {$patientName}،\n";
        $message .= "یادآوری می‌شود که نوبت شما در تاریخ {$date}";
        $message .= " ساعت {$time} می‌باشد.\n";
        $message .= "لطفاً در زمان مقرر در مطب حضور داشته باشید.";

        return $this->send($mobile, $message);
    }

    public function sendConfirmationRequest(string $mobile, string $time, string $date): array
    {
        $message = "شما {$date} ساعت {$time} نوبت دارید.\n";
        $message .= "برای تایید عدد 1 و برای لغو عدد 2 را ارسال نمایید.";

        return $this->send($mobile, $message);
    }

    public function sendExpirationAlert(string $mobile, int $daysLeft): array
    {
        $message = "کاربر گرامی،\n";
        $message .= "اشتراک سیستم مدیریت مطب شما تا {$daysLeft} روز دیگر منقضی می‌شود.\n";
        $message .= "لطفاً برای تمدید اقدام فرمایید.";

        return $this->send($mobile, $message);
    }

    public function getProviderName(): string
    {
        return $this->provider?->getProviderName() ?? 'none';
    }

    public function isConfigured(): bool
    {
        return $this->provider?->isConfigured() ?? false;
    }

    public function setProvider(string $providerName): void
    {
        config(['services.sms.provider' => $providerName]);
        $this->provider = $this->resolveProvider();
    }
}
