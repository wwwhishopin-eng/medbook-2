<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Patient extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'national_id',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'blood_type',
        'conditions',
        'allergies',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
        'notes',
        'avatar_color',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'conditions'    => 'array',
        'allergies'     => 'array',
    ];

    // ── Status constants ────────────────────────────────────────────────────

    const STATUS_ACTIVE    = 'active';
    const STATUS_PENDING   = 'pending';
    const STATUS_RECOVERED = 'recovered';
    const STATUS_INACTIVE  = 'inactive';

    const STATUSES = [
        self::STATUS_ACTIVE    => 'فعال',
        self::STATUS_PENDING   => 'در انتظار',
        self::STATUS_RECOVERED => 'بهبودیافته',
        self::STATUS_INACTIVE  => 'غیرفعال',
    ];

    const GENDER_MALE   = 'male';
    const GENDER_FEMALE = 'female';

    const BLOOD_TYPES = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    const AVATAR_COLORS = [
        '#2E5BFF', '#0E8F72', '#9333EA', '#EA580C',
        '#E11D48', '#0284C7', '#D97706', '#059669',
    ];

    // ── Accessors ───────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    public function getCodeAttribute(): string
    {
        return 'MB-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getAvatarInitialAttribute(): string
    {
        return mb_substr($this->first_name, 0, 1);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalHistory(): HasMany
    {
        return $this->hasMany(MedicalHistory::class)->latest();
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class)->latest();
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('national_id', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public static function randomAvatarColor(): string
    {
        return self::AVATAR_COLORS[array_rand(self::AVATAR_COLORS)];
    }
}