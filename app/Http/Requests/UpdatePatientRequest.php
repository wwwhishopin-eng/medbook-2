<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $patientId = $this->route('patient')?->id ?? $this->route('patient');

        return [
            'first_name'              => ['required', 'string', 'max:60'],
            'last_name'               => ['required', 'string', 'max:60'],
            'national_id'             => [
                'nullable', 'string', 'size:10', 'regex:/^\d{10}$/',
                Rule::unique('patients', 'national_id')->ignore($patientId),
            ],
            'date_of_birth'           => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
            'gender'                  => ['required', Rule::in([Patient::GENDER_MALE, Patient::GENDER_FEMALE])],
            'phone'                   => ['nullable', 'string', 'max:20', 'regex:/^0[0-9]{10}$/'],
            'email'                   => ['nullable', 'email', 'max:150'],
            'address'                 => ['nullable', 'string', 'max:500'],
            'blood_type'              => ['nullable', Rule::in(Patient::BLOOD_TYPES)],
            'conditions'              => ['nullable', 'array'],
            'conditions.*'            => ['string', 'max:100'],
            'allergies'               => ['nullable', 'array'],
            'allergies.*'             => ['string', 'max:100'],
            'emergency_contact_name'  => ['nullable', 'string', 'max:120'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20', 'regex:/^0[0-9]{10}$/'],
            'status'                  => ['required', Rule::in(array_keys(Patient::STATUSES))],
            'notes'                   => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return (new StorePatientRequest())->messages();
    }

    public function attributes(): array
    {
        return (new StorePatientRequest())->attributes();
    }
}