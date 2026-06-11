<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaitingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'preferred_date',
        'preferred_time_start',
        'preferred_time_end',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'preferred_time_start' => 'datetime:H:i',
        'preferred_time_end' => 'datetime:H:i',
    ];

    const STATUS_WAITING = 'waiting';
    const STATUS_NOTIFIED = 'notified';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_WAITING => 'در انتظار',
        self::STATUS_NOTIFIED => 'اطلاع داده شده',
        self::STATUS_ASSIGNED => 'نوبت گرفته',
        self::STATUS_CANCELLED => 'لغو شده',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_WAITING);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('preferred_date', $date);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function markAsNotified(): void
    {
        $this->status = self::STATUS_NOTIFIED;
        $this->save();
    }

    public function assign(): void
    {
        $this->status = self::STATUS_ASSIGNED;
        $this->save();
    }

    public function cancel(): void
    {
        $this->status = self::STATUS_CANCELLED;
        $this->save();
    }
}
