<div dir="rtl">

    {{-- Profile Header Card --}}
    <div class="card" style="padding:24px;margin-bottom:20px;">
        <div class="profile-header">

            {{-- Avatar --}}
            <div class="avatar"
                 style="background:{{ $patient->avatar_color }}22;color:{{ $patient->avatar_color }};
                        width:72px;height:72px;font-size:26px;border-radius:20px;flex-shrink:0;">
                {{ $patient->avatar_initial }}
            </div>

            {{-- Name & meta --}}
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:6px;">
                    <h2 style="font-size:22px;font-weight:700;color:#111A6B;margin:0;">
                        {{ $patient->full_name }}
                    </h2>
                    @php
                        $badgeStyles = [
                            'active'    => 'background:#DCFCE7;color:#15803D',
                            'pending'   => 'background:#FEF9C3;color:#854D0E',
                            'recovered' => 'background:#EEF4FF;color:#1D4ED8',
                            'inactive'  => 'background:#F3F4F6;color:#6B7280',
                        ];
                    @endphp
                    <span class="badge" style="{{ $badgeStyles[$patient->status] ?? '' }}">
                        {{ $patient->status_label }}
                    </span>
                </div>
                <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:#6B7280;">
                    <span dir="ltr" style="font-weight:600;color:#2E5BFF;">{{ $patient->code }}</span>
                    @if($patient->date_of_birth)
                        <span>{{ $patient->age }} ساله</span>
                    @endif
                    @if($patient->gender)
                        <span>{{ $patient->gender === 'male' ? 'مرد' : 'زن' }}</span>
                    @endif
                    @if($patient->blood_type)
                        <span style="background:#FEE2E2;color:#991B1B;padding:2px 8px;border-radius:20px;font-weight:600;">
                            {{ $patient->blood_type }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="profile-header-actions" style="display:flex;gap:8px;flex-shrink:0;">
                <button
                    wire:click="$dispatch('open-patient-form', { id: {{ $patient->id }} })"
                    class="btn-ghost"
                    style="font-size:13px;padding:8px 16px;">
                    ویرایش اطلاعات
                </button>
                <a href="{{ route('patients.history', $patient) }}" class="btn-primary"
                   style="font-size:13px;padding:8px 16px;text-decoration:none;display:inline-flex;align-items:center;">
                    سابقه پزشکی
                </a>
            </div>
        </div>

        {{-- Quick stats --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-top:20px;
                    padding-top:20px;border-top:1px solid #F3F4F6;">
            <div style="text-align:center;">
                <div style="font-size:22px;font-weight:700;color:#111A6B;">
                    {{ $patient->medicalHistory->count() }}
                </div>
                <div style="font-size:12px;color:#9CA3AF;">مراجعه</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:22px;font-weight:700;color:#111A6B;">
                    {{ $patient->appointments->count() }}
                </div>
                <div style="font-size:12px;color:#9CA3AF;">نوبت</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:14px;font-weight:700;color:#111A6B;">
                    {{ $patient->medicalHistory->first()?->visit_date?->format('Y/m/d') ?? '—' }}
                </div>
                <div style="font-size:12px;color:#9CA3AF;">آخرین ویزیت</div>
            </div>
            @if($patient->phone)
            <div style="text-align:center;">
                <div style="font-size:14px;font-weight:700;color:#111A6B;" dir="ltr">{{ $patient->phone }}</div>
                <div style="font-size:12px;color:#9CA3AF;">موبایل</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tab-bar" style="margin-bottom:16px;">
        @foreach(['overview' => 'اطلاعات کلی', 'history' => 'سابقه پزشکی', 'conditions' => 'بیماری‌ها', 'contact' => 'تماس'] as $tab => $label)
            <button wire:click="setTab('{{ $tab }}')"
                    style="font-weight:{{ $activeTab === $tab ? '600' : '400' }};
                           {{ $activeTab === $tab
                               ? 'background:linear-gradient(135deg,#2E5BFF,#1A3FDB);color:#fff;box-shadow:0 4px 15px rgba(46,91,255,0.3);'
                               : 'background:transparent;color:#6B7280;' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Tab: Overview --}}
    @if($activeTab === 'overview')
    <div class="grid-2-col" class="fade-up">

        {{-- Personal info --}}
        <div class="card" style="padding:20px;">
            <h4 style="font-size:14px;font-weight:700;color:#111A6B;margin:0 0 16px;">اطلاعات شخصی</h4>
            <div style="display:grid;gap:12px;">
                @foreach([
                    ['label' => 'کد ملی', 'value' => $patient->national_id, 'ltr' => true],
                    ['label' => 'تاریخ تولد', 'value' => $patient->date_of_birth?->format('Y/m/d')],
                    ['label' => 'جنسیت', 'value' => $patient->gender === 'male' ? 'مرد' : 'زن'],
                    ['label' => 'گروه خونی', 'value' => $patient->blood_type],
                    ['label' => 'آدرس', 'value' => $patient->address],
                ] as $item)
                    @if($item['value'])
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;
                                padding-bottom:10px;border-bottom:1px solid #F9FAFB;">
                        <span style="font-size:12px;color:#9CA3AF;">{{ $item['label'] }}</span>
                        <span style="font-size:13px;font-weight:500;color:#111827;text-align:left;max-width:60%;"
                              {{ ($item['ltr'] ?? false) ? 'dir=ltr' : '' }}>
                            {{ $item['value'] }}
                        </span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Doctor notes --}}
        <div class="card" style="padding:20px;">
            <h4 style="font-size:14px;font-weight:700;color:#111A6B;margin:0 0 16px;">یادداشت پزشک</h4>
            @if($patient->notes)
                <p style="font-size:13px;color:#374151;line-height:1.8;margin:0;">{{ $patient->notes }}</p>
            @else
                <p style="font-size:13px;color:#D1D5DB;text-align:center;padding:20px 0;">
                    یادداشتی ثبت نشده است.
                </p>
            @endif
        </div>

        {{-- Allergies --}}
        @if($patient->allergies && count($patient->allergies))
        <div class="card" style="padding:20px;">
            <h4 style="font-size:14px;font-weight:700;color:#991B1B;margin:0 0 14px;">
                آلرژی‌ها
            </h4>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($patient->allergies as $allergy)
                    <span style="background:#FEE2E2;color:#991B1B;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:500;">
                        {{ $allergy }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Recent visits preview --}}
        <div class="card" style="padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                <h4 style="font-size:14px;font-weight:700;color:#111A6B;margin:0;">آخرین ویزیت‌ها</h4>
                <button wire:click="setTab('history')"
                        style="font-size:12px;color:#2E5BFF;background:none;border:none;cursor:pointer;">
                    مشاهده همه
                </button>
            </div>
            @forelse($patient->medicalHistory->take(3) as $entry)
                <div style="display:flex;justify-content:space-between;align-items:center;
                            padding:8px 0;border-bottom:1px solid #F3F4F6;">
                    <div>
                        <div style="font-size:13px;font-weight:500;color:#111827;">
                            {{ $entry->visit_type_label }}
                        </div>
                        <div style="font-size:11px;color:#9CA3AF;">
                            {{ $entry->diagnosis ?? 'بدون تشخیص' }}
                        </div>
                    </div>
                    <span style="font-size:12px;color:#6B7280;">
                        {{ $entry->visit_date->format('Y/m/d') }}
                    </span>
                </div>
            @empty
                <p style="font-size:13px;color:#D1D5DB;text-align:center;padding:12px 0;">سابقه‌ای ثبت نشده.</p>
            @endforelse
        </div>

    </div>
    @endif

    {{-- Tab: Medical History --}}
    @if($activeTab === 'history')
    <div class="fade-up">
        @livewire('patient.patient-history', ['patient' => $patient], key('history-'.$patient->id))
    </div>
    @endif

    {{-- Tab: Conditions --}}
    @if($activeTab === 'conditions')
    <div class="card" style="padding:24px;" class="fade-up">
        <h4 style="font-size:15px;font-weight:700;color:#111A6B;margin:0 0 18px;">بیماری‌های زمینه‌ای</h4>
        @if($patient->conditions && count($patient->conditions))
            <div style="display:flex;flex-wrap:wrap;gap:10px;">
                @foreach($patient->conditions as $condition)
                    <span style="background:#EEF4FF;color:#2E5BFF;padding:8px 18px;border-radius:20px;
                                 font-size:13px;font-weight:500;border:1px solid #BBCFFF;">
                        {{ $condition }}
                    </span>
                @endforeach
            </div>
        @else
            <p style="font-size:14px;color:#9CA3AF;text-align:center;padding:32px 0;">
                بیماری زمینه‌ای ثبت نشده است.
            </p>
        @endif
    </div>
    @endif

    {{-- Tab: Contact --}}
    @if($activeTab === 'contact')
    <div class="grid-2-col" class="fade-up">
        <div class="card" style="padding:20px;">
            <h4 style="font-size:14px;font-weight:700;color:#111A6B;margin:0 0 16px;">اطلاعات تماس</h4>
            <div style="display:grid;gap:14px;">
                @if($patient->phone)
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:36px;height:36px;background:#EEF4FF;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2E5BFF" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 01.22 2.18 2 2 0 012.18 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.09a16 16 0 006 6l.56-.56a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#9CA3AF;">موبایل</div>
                        <div style="font-size:14px;font-weight:600;" dir="ltr">{{ $patient->phone }}</div>
                    </div>
                </div>
                @endif
                @if($patient->email)
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:36px;height:36px;background:#EDFAF6;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0E8F72" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#9CA3AF;">ایمیل</div>
                        <div style="font-size:14px;font-weight:600;" dir="ltr">{{ $patient->email }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($patient->emergency_contact_name)
        <div class="card" style="padding:20px;border:2px solid #FEE2E2;">
            <h4 style="font-size:14px;font-weight:700;color:#991B1B;margin:0 0 16px;">تماس اضطراری</h4>
            <div style="font-size:15px;font-weight:600;color:#111827;margin-bottom:4px;">
                {{ $patient->emergency_contact_name }}
            </div>
            @if($patient->emergency_contact_phone)
                <div style="font-size:14px;color:#6B7280;" dir="ltr">
                    {{ $patient->emergency_contact_phone }}
                </div>
            @endif
        </div>
        @endif
    </div>
    @endif

</div>
