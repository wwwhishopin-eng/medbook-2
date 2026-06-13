<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => 'required|url',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'user_id' => auth()->id(),
                'p256dh_key' => $data['keys']['p256dh'],
                'auth_token' => $data['keys']['auth'],
            ]
        );

        return response()->json(['success' => true]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate(['endpoint' => 'required|url']);

        PushSubscription::where('endpoint', $request->endpoint)
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['success' => true]);
    }

    public function status(): JsonResponse
    {
        $hasSubscription = PushSubscription::where('user_id', auth()->id())->exists();

        return response()->json(['subscribed' => $hasSubscription]);
    }

    public function vapidPublicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('services.webpush.vapid.public_key', ''),
        ]);
    }
}
