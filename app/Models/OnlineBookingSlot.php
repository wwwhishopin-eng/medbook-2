<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class OnlineBookingSlot extends Model
{
    protected $fillable = [
        'user_id',
        'slug',
        'is_active',
        'working_hours',
        'slot_duration',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'working_hours' => 'array',
        'slot_duration' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(OnlineBooking::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $slot) {
            if (empty($slot->slug)) {
                $slot->slug = Str::slug($slot->user->name ?? 'clinic') . '-' . Str::random(6);
            }
            if (empty($slot->working_hours)) {
                $slot->working_hours = [
                    ['day' => 0, 'start' => '08:00', 'end' => '20:00'],
                    ['day' => 1, 'start' => '08:00', 'end' => '20:00'],
                    ['day' => 2, 'start' => '08:00', 'end' => '20:00'],
                    ['day' => 3, 'start' => '08:00', 'end' => '20:00'],
                    ['day' => 4, 'start' => '08:00', 'end' => '20:00'],
                    ['day' => 5, 'start' => '08:00', 'end' => '14:00'],
                ];
            }
        });
    }
}
