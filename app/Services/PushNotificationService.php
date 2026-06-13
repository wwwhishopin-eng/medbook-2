<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;

class PushNotificationService
{
    public function sendToUser(User $user, string $title, string $body, ?string $url = null): array
    {
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();
        $results = ['sent' => 0, 'failed' => 0];

        foreach ($subscriptions as $sub) {
            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'icon' => '/icons/icon-192x192.svg',
                'url' => $url,
            ]);

            $result = $this->sendWebPush($sub, $payload);
            if ($result) {
                $results['sent']++;
            } else {
                $results['failed']++;
                $sub->delete();
            }
        }

        return $results;
    }

    public function sendToAll(string $title, string $body, ?string $url = null): array
    {
        $results = ['sent' => 0, 'failed' => 0];
        $subscriptions = PushSubscription::with('user')->get();

        foreach ($subscriptions as $sub) {
            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'icon' => '/icons/icon-192x192.svg',
                'url' => $url,
            ]);

            $result = $this->sendWebPush($sub, $payload);
            if ($result) {
                $results['sent']++;
            } else {
                $results['failed']++;
                $sub->delete();
            }
        }

        return $results;
    }

    private function sendWebPush(PushSubscription $subscription, string $payload): bool
    {
        $publicKey = config('services.webpush.vapid.public_key');
        $privateKey = config('services.webpush.vapid.private_key');

        if (!$publicKey || !$privateKey) {
            return false;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $subscription->endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'TTL: 86400',
                'Content-Encoding: aesgcm',
            ],
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $statusCode >= 200 && $statusCode < 300;
    }
}
