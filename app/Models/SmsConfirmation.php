<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsConfirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'mobile',
        'message_type',
        'status',
        'sent_at',
        'responded_at',
        'response',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    const TYPE_CONFIRMATION_REQUEST = 'confirmation_request';
    const TYPE_REMINDER_24H = 'reminder_24h';
    const TYPE_REMINDER_2H = 'reminder_2h';
    const TYPE_VOICE_REMINDER = 'voice_reminder';

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    const RESPONSE_CONFIRM = '1';
    const RESPONSE_CANCEL = '2';

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function confirm(): void
    {
        $this->status = self::STATUS_CONFIRMED;
        $this->response = self::RESPONSE_CONFIRM;
        $this->responded_at = now();
        $this->save();

        // Update appointment status
        if ($this->appointment) {
            $this->appointment->status = Appointment::STATUS_CONFIRMED;
            $this->appointment->save();
        }
    }

    public function cancelByPatient(): void
    {
        $this->status = self::STATUS_CANCELLED;
        $this->response = self::RESPONSE_CANCEL;
        $this->responded_at = now();
        $this->save();

        // Update appointment status
        if ($this->appointment) {
            $this->appointment->status = Appointment::STATUS_CANCELLED;
            $this->appointment->save();
        }
    }

    public static function createForAppointment(Appointment $appointment, string $type = self::TYPE_CONFIRMATION_REQUEST): self
    {
        return self::create([
            'appointment_id' => $appointment->id,
            'mobile' => $appointment->patient->phone ?? '',
            'message_type' => $type,
            'status' => self::STATUS_PENDING,
            'sent_at' => now(),
        ]);
    }
}
