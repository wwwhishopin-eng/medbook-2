<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    const ROLE_DOCTOR = 'doctor';
    const ROLE_OPERATOR = 'operator';

    const ROLES = [
        self::ROLE_DOCTOR => 'پزشک',
        self::ROLE_OPERATOR => 'پذیرش',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isDoctor(): bool
    {
        return $this->role === self::ROLE_DOCTOR;
    }

    public function isOperator(): bool
    {
        return $this->role === self::ROLE_OPERATOR;
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function can($abilities, $arguments = []): bool
    {
        // Handle simple string permission checks against your role-based permissions
        if (is_string($abilities) && empty($arguments)) {
            return in_array($abilities, $this->getPermissions());
        }

        // Fall back to Laravel's default Gate/Policy handling for everything else
        return parent::can($abilities, $arguments);
    }

    public function getPermissions(): array
    {
        return match ($this->role) {
            self::ROLE_DOCTOR => [
                'view_appointments',
                'view_patients',
                'create_medical_reports',
                'upload_files',
                'record_voice_notes',
                'view_debts',
                'view_payments',
                'view_statistics',
                'manage_subscription',
                'manage_clinic_settings',
            ],
            self::ROLE_OPERATOR => [
                'create_appointments',
                'edit_appointments',
                'cancel_appointments',
                'register_patients',
                'search_patients',
                'register_payments',
                'view_debts',
                'manage_waiting_list',
                'manage_daily_schedule',
            ],
            default => [],
        };
    }
}
