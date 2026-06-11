<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Helpers\JalaliDate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class LicenseService
{
    const CACHE_TTL = 300; // 5 minutes
    const GRACE_PERIOD_DAYS = 3;

    private ?Subscription $cachedSubscription = null;

    public function getActiveSubscription(): ?Subscription
    {
        if (!auth()->check()) {
            return null;
        }

        if ($this->cachedSubscription !== null) {
            return $this->cachedSubscription;
        }

        $userId = auth()->id();
        $cacheKey = "subscription:user:{$userId}";

        $subscription = Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return Subscription::query()
                ->with('plan')
                ->where('user_id', auth()->id())
                ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL])
                ->orderByDesc('id')
                ->first();
        });

        $this->cachedSubscription = $subscription;
        return $subscription;
    }

    public function isValidLicense(): bool
    {
        $subscription = $this->getActiveSubscription();

        if (!$subscription) {
            return $this->isWithinGracePeriod();
        }

        if ($subscription->status === Subscription::STATUS_EXPIRED) {
            return $this->isWithinGracePeriod();
        }

        if ($subscription->isExpired()) {
            $subscription->expire();
            return $this->isWithinGracePeriod();
        }

        return true;
    }

    public function isSystemLocked(): bool
    {
        return !$this->isValidLicense();
    }

    public function getRemainingDays(): int
    {
        $subscription = $this->getActiveSubscription();

        if (!$subscription) {
            return 0;
        }

        if ($subscription->isExpired()) {
            $graceDays = $this->getGracePeriodRemaining();
            return -$graceDays; // Negative to indicate past expiration
        }

        return $subscription->daysRemaining();
    }

    public function getExpiryStatus(): array
    {
        $subscription = $this->getActiveSubscription();
        $days = $this->getRemainingDays();

        $expiresAt = $subscription?->expires_at ? JalaliDate::format($subscription->expires_at, 'Y/m/d') : '';
        $isNear = $days > 0 && $days <= 7;
        $showAlert = $days > 0 && $days <= 7;
        $critical = $days > 0 && $days <= 3;
        $expired = $days <= 0;

        return [
            'valid' => $this->isValidLicense(),
            'locked' => $this->isSystemLocked(),
            'days_remaining' => $days,
            'expires_at' => $expiresAt,
            'expires_at_raw' => $subscription?->expires_at,
            'near_expiration' => $isNear,
            'show_alert' => $showAlert,
            'critical' => $critical,
            'expired' => $expired,
            'status_label' => $subscription?->status_label ?: 'بدون اشتراک',
            'plan_name' => $subscription?->plan?->name ?: '',
            'license_key' => $subscription?->license_key ?: '',
        ];
    }

    public function shouldShowExpirationAlert(): bool
    {
        $status = $this->getExpiryStatus();
        return $status['show_alert'] || $status['expired'];
    }

    public function shouldLockSystem(): bool
    {
        return $this->isSystemLocked();
    }

    public function refreshCache(): void
    {
        $userId = auth()->id();
        Cache::forget("subscription:user:{$userId}");
        $this->cachedSubscription = null;
    }

    private function isWithinGracePeriod(): bool
    {
        $subscription = Subscription::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->first();

        if (!$subscription || !$subscription->expires_at) {
            return false;
        }

        return now()->diffInDays($subscription->expires_at, false) <= self::GRACE_PERIOD_DAYS;
    }

    private function getGracePeriodRemaining(): int
    {
        $subscription = Subscription::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->first();

        if (!$subscription || !$subscription->expires_at) {
            return 0;
        }

        $expiredDays = now()->diffInDays($subscription->expires_at, false);
        return max(0, self::GRACE_PERIOD_DAYS + $expiredDays);
    }

    public function createTrialSubscription(User $user, int $days = 14): Subscription
    {
        $plan = Subscription::where('slug', 'basic')->first()
            ?? SubscriptionPlan::where('is_active', true)->first();

        if (!$plan) {
            throw new Exception('No active plan found for trial subscription');
        }

        return Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_TRIAL,
            'license_key' => Subscription::generateLicenseKey(),
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays($days),
            'expires_at' => now()->addDays($days),
        ]);
    }
}
