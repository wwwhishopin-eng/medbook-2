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

    const STATUS_RESERVED   = 'reserved';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_ARRIVED    = 'arrived';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_CANCELLED  = 'cancelled';
    const STATUS_NO_SHOW    = 'no_show';

    const STATUSES = [
        self::STATUS_RESERVED  => 'رزرو شده',
        self::STATUS_CONFIRMED => 'تایید شده',
        self::STATUS_ARRIVED   => 'حاضر',
        self::STATUS_COMPLETED => 'انجام شده',
        self::STATUS_CANCELLED => 'لغو شده',
        self::STATUS_NO_SHOW   => 'غایب',
    ];

    const STATUS_COLORS = [
        self::STATUS_RESERVED  => 'yellow',
        self::STATUS_CONFIRMED => 'green',
        self::STATUS_ARRIVED   => 'blue',
        self::STATUS_COMPLETED => 'green',
        self::STATUS_CANCELLED => 'red',
        self::STATUS_NO_SHOW   => 'gray',
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

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'gray';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusBadgeStyleAttribute(): string
    {
        return match ($this->status_color) {
            'green'  => 'background:#DCFCE7;color:#15803D',
            'yellow' => 'background:#FEF9C3;color:#854D0E',
            'blue'   => 'background:#EEF4FF;color:#1D4ED8',
            'red'    => 'background:#FEE2E2;color:#991B1B',
            default  => 'background:#F3F4F6;color:#6B7280',
        };
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
                     ->whereIn('status', [self::STATUS_RESERVED, self::STATUS_CONFIRMED]);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('start_at', today());
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_RESERVED, self::STATUS_CONFIRMED]);
    }
}