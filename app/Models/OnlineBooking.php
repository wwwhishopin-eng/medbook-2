<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineBooking extends Model
{
    protected $fillable = [
        'online_booking_slot_id',
        'patient_name',
        'patient_phone',
        'patient_national_id',
        'start_at',
        'end_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING => 'در انتظار تایید',
        self::STATUS_CONFIRMED => 'تایید شده',
        self::STATUS_CANCELLED => 'لغو شده',
    ];

    public function bookingSlot(): BelongsTo
    {
        return $this->belongsTo(OnlineBookingSlot::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
