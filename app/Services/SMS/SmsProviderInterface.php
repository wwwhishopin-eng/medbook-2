<?php

namespace App\Services\SMS;

interface SmsProviderInterface
{
    public function send(string $mobile, string $message): array;
    public function sendBulk(array $recipients, string $message): array;
    public function getProviderName(): string;
    public function isConfigured(): bool;
}
