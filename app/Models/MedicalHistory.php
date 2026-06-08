<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalHistory extends Model
{
    use HasFactory;

    protected $table = 'medical_histories';

    protected $fillable = [
        'patient_id',
        'visit_date',
        'visit_type',
        'chief_complaint',
        'diagnosis',
        'treatment',
        'prescriptions',
        'lab_results',
        'vital_signs',
        'doctor_notes',
        'follow_up_date',
        'created_by',
    ];

    protected $casts = [
        'visit_date'    => 'date',
        'follow_up_date'=> 'date',
        'vital_signs'   => 'array',
        'lab_results'   => 'array',
    ];

    const VISIT_TYPES = [
        'first_visit'  => 'ویزیت اول',
        'follow_up'    => 'پیگیری',
        'lab_review'   => 'کنترل آزمایش',
        'consultation' => 'مشاوره',
        'emergency'    => 'اورژانس',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getVisitTypeLabelAttribute(): string
    {
        return self::VISIT_TYPES[$this->visit_type] ?? $this->visit_type;
    }
}
