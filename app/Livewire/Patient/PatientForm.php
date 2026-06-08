<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PatientForm extends Component
{
    public ?Patient $patient = null;

    // Form fields
    public string $first_name              = '';
    public string $last_name               = '';
    public string $national_id             = '';
    public string $date_of_birth           = '';
    public string $gender                  = 'male';
    public string $phone                   = '';
    public string $email                   = '';
    public string $address                 = '';
    public string $blood_type              = '';
    public string $conditions_raw          = '';  // comma-separated input
    public string $allergies_raw           = '';
    public string $emergency_contact_name  = '';
    public string $emergency_contact_phone = '';
    public string $status                  = 'pending';
    public string $notes                   = '';

    public bool $isOpen = false;

    // ── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(?Patient $patient = null): void
    {
        if ($patient && $patient->exists) {
            $this->patient = $patient;
            $this->fillForm($patient);
        }
    }

    public function openModal(?int $patientId = null): void
    {
        $this->resetForm();
        if ($patientId) {
            $this->patient = Patient::findOrFail($patientId);
            $this->fillForm($this->patient);
        }
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    private function fillForm(Patient $patient): void
    {
        $this->first_name              = $patient->first_name;
        $this->last_name               = $patient->last_name;
        $this->national_id             = $patient->national_id ?? '';
        $this->date_of_birth           = $patient->date_of_birth?->format('Y-m-d') ?? '';
        $this->gender                  = $patient->gender;
        $this->phone                   = $patient->phone ?? '';
        $this->email                   = $patient->email ?? '';
        $this->address                 = $patient->address ?? '';
        $this->blood_type              = $patient->blood_type ?? '';
        $this->conditions_raw          = implode(', ', $patient->conditions ?? []);
        $this->allergies_raw           = implode(', ', $patient->allergies ?? []);
        $this->emergency_contact_name  = $patient->emergency_contact_name ?? '';
        $this->emergency_contact_phone = $patient->emergency_contact_phone ?? '';
        $this->status                  = $patient->status;
        $this->notes                   = $patient->notes ?? '';
    }

    private function resetForm(): void
    {
        $this->patient                 = null;
        $this->first_name              = '';
        $this->last_name               = '';
        $this->national_id             = '';
        $this->date_of_birth           = '';
        $this->gender                  = 'male';
        $this->phone                   = '';
        $this->email                   = '';
        $this->address                 = '';
        $this->blood_type              = '';
        $this->conditions_raw          = '';
        $this->allergies_raw           = '';
        $this->emergency_contact_name  = '';
        $this->emergency_contact_phone = '';
        $this->status                  = 'pending';
        $this->notes                   = '';
        $this->resetValidation();
    }

    // ── Validation ───────────────────────────────────────────────────────────

    protected function validationRules(): array
    {
        $patientId = $this->patient?->id;

        return [
            'first_name'              => ['required', 'string', 'max:60'],
            'last_name'               => ['required', 'string', 'max:60'],
            'national_id'             => [
                'nullable', 'string', 'size:10', 'regex:/^\d{10}$/',
                Rule::unique('patients', 'national_id')->ignore($patientId),
            ],
            'date_of_birth'           => ['nullable', 'date', 'before:today'],
            'gender'                  => ['required', Rule::in(['male', 'female'])],
            'phone'                   => ['nullable', 'string', 'max:20'],
            'email'                   => ['nullable', 'email', 'max:150'],
            'address'                 => ['nullable', 'string', 'max:500'],
            'blood_type'              => ['nullable', Rule::in(Patient::BLOOD_TYPES)],
            'conditions_raw'          => ['nullable', 'string', 'max:500'],
            'allergies_raw'           => ['nullable', 'string', 'max:500'],
            'emergency_contact_name'  => ['nullable', 'string', 'max:120'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'status'                  => ['required', Rule::in(array_keys(Patient::STATUSES))],
            'notes'                   => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function validationMessages(): array
    {
        return [
            'first_name.required'  => 'نام الزامی است.',
            'last_name.required'   => 'نام خانوادگی الزامی است.',
            'national_id.size'     => 'کد ملی باید ۱۰ رقم باشد.',
            'national_id.unique'   => 'این کد ملی قبلاً ثبت شده است.',
            'email.email'          => 'ایمیل معتبر نیست.',
        ];
    }

    // ── Save ─────────────────────────────────────────────────────────────────

    public function save(): void
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());

        $data = collect($validated)
            ->except(['conditions_raw', 'allergies_raw'])
            ->merge([
                'conditions' => $this->parseTagsInput($this->conditions_raw),
                'allergies'  => $this->parseTagsInput($this->allergies_raw),
            ])
            ->filter(fn ($v) => $v !== '')
            ->toArray();

        if ($this->patient && $this->patient->exists) {
            $this->patient->update($data);
            $message = "اطلاعات بیمار «{$this->patient->full_name}» به‌روزرسانی شد.";
        } else {
            $data['avatar_color'] = Patient::randomAvatarColor();
            $patient = Patient::create($data);
            $message = "بیمار «{$patient->full_name}» با موفقیت اضافه شد.";
        }

        $this->closeModal();
        $this->dispatch('patient-saved');
        $this->dispatch('notify', message: $message, type: 'success');
    }

    private function parseTagsInput(string $raw): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', $raw))
        ));
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.patient.patient-form', [
            'statuses'   => Patient::STATUSES,
            'bloodTypes' => Patient::BLOOD_TYPES,
        ]);
    }
}