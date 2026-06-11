<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'features',
        'max_patients',
        'max_appointments_per_day',
        'max_users',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price_monthly' => 'integer',
        'price_yearly' => 'integer',
        'max_patients' => 'integer',
        'max_appointments_per_day' => 'integer',
        'max_users' => 'integer',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_TRIAL = 'trial';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SUSPENDED = 'suspended';

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function hasFeature(string $feature): bool
    {
        return isset($this->features[$feature]) && $this->features[$feature] !== false;
    }

    public function getFeature(string $feature, $default = null)
    {
        return $this->features[$feature] ?? $default;
    }

    public function formattedPriceMonthly(): string
    {
        return \App\Helpers\Persian::currency($this->price_monthly);
    }

    public function formattedPriceYearly(): string
    {
        return \App\Helpers\Persian::currency($this->price_yearly);
    }

    public static function getDefaultPlan(): ?self
    {
        return static::where('slug', 'professional')->first()
            ?? static::where('is_active', true)->orderBy('sort_order')->first();
    }
}
