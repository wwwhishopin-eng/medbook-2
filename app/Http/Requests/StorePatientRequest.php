<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'              => ['required', 'string', 'max:60'],
            'last_name'               => ['required', 'string', 'max:60'],
            'national_id'             => ['nullable', 'string', 'size:10', 'regex:/^\d{10}$/', 'unique:patients,national_id'],
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
        return [
            'first_name.required'    => 'نام الزامی است.',
            'last_name.required'     => 'نام خانوادگی الزامی است.',
            'national_id.size'       => 'کد ملی باید ۱۰ رقم باشد.',
            'national_id.regex'      => 'کد ملی فقط باید شامل اعداد باشد.',
            'national_id.unique'     => 'این کد ملی قبلاً ثبت شده است.',
            'date_of_birth.before'   => 'تاریخ تولد باید در گذشته باشد.',
            'phone.regex'            => 'شماره موبایل باید با ۰ شروع شود و ۱۱ رقم باشد.',
            'gender.in'              => 'جنسیت نامعتبر است.',
            'blood_type.in'          => 'گروه خونی نامعتبر است.',
            'status.in'              => 'وضعیت نامعتبر است.',
            'email.email'            => 'آدرس ایمیل معتبر نیست.',
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name'              => 'نام',
            'last_name'               => 'نام خانوادگی',
            'national_id'             => 'کد ملی',
            'date_of_birth'           => 'تاریخ تولد',
            'gender'                  => 'جنسیت',
            'phone'                   => 'شماره موبایل',
            'email'                   => 'ایمیل',
            'blood_type'              => 'گروه خونی',
            'status'                  => 'وضعیت',
        ];
    }
}