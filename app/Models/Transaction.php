<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'appointment_id',
        'created_by',
        'description',
        'type',
        'amount',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'integer',
    ];

    const TYPE_CHARGE = 'charge';
    const TYPE_PAYMENT = 'payment';

    const TYPES = [
        self::TYPE_CHARGE => 'هزینه',
        self::TYPE_PAYMENT => 'پرداخت',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getFormattedAmountAttribute(): string
    {
        return \App\Helpers\Persian::currency($this->amount);
    }

    public static function getDebtForPatient(int $patientId): int
    {
        $charges = static::where('patient_id', $patientId)->where('type', self::TYPE_CHARGE)->sum('amount');
        $payments = static::where('patient_id', $patientId)->where('type', self::TYPE_PAYMENT)->sum('amount');
        return max(0, $charges - $payments);
    }

    public static function getTotalDebt(): int
    {
        $charges = static::where('type', self::TYPE_CHARGE)->sum('amount');
        $payments = static::where('type', self::TYPE_PAYMENT)->sum('amount');
        return max(0, $charges - $payments);
    }
}
