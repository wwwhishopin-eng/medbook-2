<?php

namespace App\Models;

use App\Helpers\JalaliDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'license_key',
        'trial_ends_at',
        'starts_at',
        'expires_at',
        'cancelled_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'date',
        'starts_at' => 'date',
        'expires_at' => 'date',
        'cancelled_at' => 'date',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_TRIAL = 'trial';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SUSPENDED = 'suspended';

    const STATUSES = [
        self::STATUS_PENDING => 'در انتظار پرداخت',
        self::STATUS_ACTIVE => 'فعال',
        self::STATUS_TRIAL => 'نسخه آزمایشی',
        self::STATUS_EXPIRED => 'منقضی شده',
        self::STATUS_CANCELLED => 'لغو شده',
        self::STATUS_SUSPENDED => 'معلق',
    ];

    const STATUS_COLORS = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_ACTIVE => 'success',
        self::STATUS_TRIAL => 'info',
        self::STATUS_EXPIRED => 'danger',
        self::STATUS_CANCELLED => 'secondary',
        self::STATUS_SUSPENDED => 'dark',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isTrial(): bool
    {
        return $this->status === self::STATUS_TRIAL;
    }

    public function daysRemaining(): int
    {
        if (!$this->expires_at) return 0;
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function isNearExpiration(int $days = 7): bool
    {
        return $this->daysRemaining() <= $days && $this->daysRemaining() > 0;
    }

    public function activate(Carbon $expiresAt): void
    {
        $this->status = self::STATUS_ACTIVE;
        $this->starts_at = now();
        $this->expires_at = $expiresAt;
        $this->save();
    }

    public function renew(int $months): void
    {
        $this->expires_at = $this->expires_at->copy()->addMonths($months);
        $this->status = self::STATUS_ACTIVE;
        $this->cancelled_at = null;
        $this->save();
    }

    public function cancel(): void
    {
        $this->cancelled_at = now();
        $this->status = self::STATUS_CANCELLED;
        $this->save();
    }

    public function expire(): void
    {
        $this->status = self::STATUS_EXPIRED;
        $this->save();
    }

    public static function generateLicenseKey(): string
    {
        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $segments[] = strtoupper(Str::random(4));
        }
        return implode('-', $segments);
    }

    public function getExpiryDateJalaliAttribute(): string
    {
        return $this->expires_at ? JalaliDate::format($this->expires_at, 'Y/m/d') : '';
    }

    public function getStartDateJalaliAttribute(): string
    {
        return $this->starts_at ? JalaliDate::format($this->starts_at, 'Y/m/d') : '';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public static function getActiveSubscriptionForUser(int $userId): ?self
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('status', self::STATUS_ACTIVE)
            ->where('expires_at', '>', now())
            ->with('plan')
            ->latest()
            ->first();
    }
}
