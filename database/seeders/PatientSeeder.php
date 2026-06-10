<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\MedicalHistory;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            [
                'first_name'   => 'علی',
                'last_name'    => 'محمدی',
                'national_id'  => '0012345678',
                'date_of_birth'=> '1975-04-12',
                'gender'       => 'male',
                'phone'        => '09123456789',
                'email'        => 'ali.mohammadi@example.com',
                'blood_type'   => 'A+',
                'status'       => 'active',
                'conditions'   => ['دیابت نوع ۲', 'فشار خون'],
                'allergies'    => ['پنی‌سیلین'],
                'notes'        => 'بیمار منظم است. کنترل قند خون هر ۳ ماه.',
            ],
            [
                'first_name'   => 'فاطمه',
                'last_name'    => 'رضایی',
                'national_id'  => '0098765432',
                'date_of_birth'=> '1990-11-03',
                'gender'       => 'female',
                'phone'        => '09351234567',
                'email'        => 'fatemeh.rezaei@example.com',
                'blood_type'   => 'O+',
                'status'       => 'active',
                'conditions'   => ['آسم'],
                'allergies'    => ['آسپرین', 'گرده گل'],
                'notes'        => 'در صورت حمله آسم، بدون تأخیر اسپری استفاده شود.',
            ],
            [
                'first_name'   => 'محمد',
                'last_name'    => 'کریمی',
                'national_id'  => '0056781234',
                'date_of_birth'=> '1965-07-20',
                'gender'       => 'male',
                'phone'        => '09211111111',
                'blood_type'   => 'B-',
                'status'       => 'pending',
                'conditions'   => ['کلسترول بالا', 'چاقی'],
                'allergies'    => [],
                'emergency_contact_name'  => 'سارا کریمی',
                'emergency_contact_phone' => '09212222222',
            ],
            [
                'first_name'   => 'زهرا',
                'last_name'    => 'احمدی',
                'national_id'  => '0034569870',
                'date_of_birth'=> '1988-02-14',
                'gender'       => 'female',
                'phone'        => '09363333333',
                'blood_type'   => 'AB+',
                'status'       => 'recovered',
                'conditions'   => [],
                'allergies'    => [],
                'notes'        => 'بهبود کامل پس از جراحی آپاندیس. پیگیری لازم نیست.',
            ],
            [
                'first_name'   => 'حسین',
                'last_name'    => 'موسوی',
                'national_id'  => '0078904321',
                'date_of_birth'=> '1952-09-01',
                'gender'       => 'male',
                'phone'        => '09124444444',
                'blood_type'   => 'O-',
                'status'       => 'active',
                'conditions'   => ['قند خون', 'آرتروز', 'نارسایی کلیه مرحله ۲'],
                'allergies'    => ['سولفا'],
                'emergency_contact_name'  => 'علیرضا موسوی',
                'emergency_contact_phone' => '09125555555',
                'notes'        => 'مصرف داروهای NSAIDs ممنوع. کنترل کراتینین ماهیانه.',
            ],
            [
                'first_name'   => 'مریم',
                'last_name'    => 'حسینی',
                'national_id'  => '0023456781',
                'date_of_birth'=> '2000-06-30',
                'gender'       => 'female',
                'phone'        => '09376666666',
                'blood_type'   => 'A-',
                'status'       => 'active',
                'conditions'   => ['میگرن'],
                'allergies'    => [],
            ],
            [
                'first_name'   => 'رضا',
                'last_name'    => 'نجفی',
                'national_id'  => '0067893210',
                'date_of_birth'=> '1983-12-25',
                'gender'       => 'male',
                'phone'        => '09197777777',
                'blood_type'   => 'B+',
                'status'       => 'inactive',
                'conditions'   => [],
                'allergies'    => [],
            ],
            [
                'first_name'   => 'نرگس',
                'last_name'    => 'صادقی',
                'national_id'  => '0011122334',
                'date_of_birth'=> '1995-08-17',
                'gender'       => 'female',
                'phone'        => '09308888888',
                'blood_type'   => 'O+',
                'status'       => 'active',
                'conditions'   => ['کم‌خونی فقر آهن'],
                'allergies'    => ['لاکتوز'],
            ],
        ];

        foreach ($patients as $data) {
            $patient = Patient::create([
                ...$data,
                'avatar_color' => Patient::randomAvatarColor(),
            ]);

            // Seed 2–4 medical history entries per patient
            $visitCount = rand(2, 4);
            for ($i = $visitCount; $i >= 1; $i--) {
                MedicalHistory::create([
                    'patient_id'     => $patient->id,
                    'visit_date'     => now()->subMonths($i * 3)->toDateString(),
                    'visit_type'     => collect(['first_visit', 'follow_up', 'lab_review', 'consultation'])->random(),
                    'chief_complaint'=> collect(['سردرد شدید', 'دردعضلانی', 'کنترل دوره‌ای', 'تنگی نفس', 'درد شکم'])->random(),
                    'diagnosis'      => collect(['بررسی بیشتر لازم است', 'وضعیت پایدار', 'بهبود قابل توجه'])->random(),
                    'treatment'      => 'ادامه دارودرمانی فعلی',
                    'doctor_notes'   => 'بیمار همکاری خوبی داشت.',
                    'follow_up_date' => now()->subMonths(($i - 1) * 3)->toDateString(),
                ]);
            }

            // Seed 1–2 upcoming appointments
            Appointment::create([
                'patient_id' => $patient->id,
                'title'      => 'ویزیت دوره‌ای',
                'start_at'   => now()->addDays(rand(1, 30)),
                'end_at'     => now()->addDays(rand(1, 30))->addMinutes(30),
                'status'     => 'reserved',
                'type'       => 'follow_up',
            ]);
        }

        $this->command->info('✅ ' . count($patients) . ' patients seeded with history and appointments.');
    }
}