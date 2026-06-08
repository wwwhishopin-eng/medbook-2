<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'created_by',
        'title',
        'start_at',
        'end_at',
        'status',
        'type',
        'notes',
        'reminder_sent',
    ];

    protected $casts = [
        'start_at'      => 'datetime',
        'end_at'        => 'datetime',
        'reminder_sent' => 'boolean',
    ];

    // ── Constants ─────────────────────────────────────────────────────────────

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW   = 'no_show';

    const STATUSES = [
        self::STATUS_SCHEDULED => 'رزرو شده',
        self::STATUS_COMPLETED => 'انجام شده',
        self::STATUS_CANCELLED => 'لغو شده',
        self::STATUS_NO_SHOW   => 'غایب',
    ];

    const TYPES = [
        'checkup'      => 'معاینه عمومی',
        'follow_up'    => 'پیگیری',
        'lab'          => 'آزمایش',
        'consultation' => 'مشاوره',
        'emergency'    => 'اورژانس',
    ];

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_at', '>=', now())
                     ->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('start_at', today());
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }
}