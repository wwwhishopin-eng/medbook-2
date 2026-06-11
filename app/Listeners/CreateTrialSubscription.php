<?php

namespace App\Listeners;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class CreateTrialSubscription
{
    public function handle(Registered $event): void
    {
        $user = $event->user;

        $existingSubscription = Subscription::where('user_id', $user->id)->first();

        if ($existingSubscription) {
            return;
        }

        $plan = SubscriptionPlan::where('slug', 'professional')->first()
            ?? SubscriptionPlan::first();

        if (!$plan) {
            Log::warning('No subscription plan found for trial subscription');
            return;
        }

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_TRIAL,
            'license_key' => Subscription::generateLicenseKey(),
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays(14),
            'expires_at' => now()->addDays(14),
        ]);

        Log::info('Trial subscription created', ['user_id' => $user->id]);
    }
}
