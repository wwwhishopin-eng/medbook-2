<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Prescription extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'patient_id',
        'medical_history_id',
        'created_by',
        'prescribed_at',
        'medications',
        'instructions',
        'duration_days',
        'refills_allowed',
        'notes',
    ];

    protected $casts = [
        'prescribed_at'  => 'date',
        'medications'    => 'array',   // [['name'=>'...','dose'=>'...','frequency'=>'...'], ...]
        'refills_allowed'=> 'integer',
        'duration_days'  => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicalHistory(): BelongsTo
    {
        return $this->belongsTo(MedicalHistory::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getMedicationListAttribute(): string
    {
        if (!$this->medications) return '—';

        return collect($this->medications)
            ->map(fn ($m) => "{$m['name']} {$m['dose']} — {$m['frequency']}")
            ->implode('، ');
    }
}