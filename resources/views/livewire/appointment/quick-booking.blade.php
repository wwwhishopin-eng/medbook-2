<div dir="rtl" class="card" style="padding:24px;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <h3 style="font-size:18px;font-weight:700;color:#111A6B;margin:0;">
            ثبت سریع نوبت
        </h3>
        <span style="font-size:11px;background:#DCFCE7;color:#15803D;padding:4px 10px;border-radius:20px;">
            کمتر از ۳ کلیک
        </span>
    </div>

    {{-- Step 1: Enter phone number --}}
    <div style="margin-bottom:20px;">
        <label class="field-label">شماره موبایل بیمار</label>
        <div style="position:relative;">
            <input
                wire:model.live.debounce.300ms="searchPhone"
                class="form-input"
                placeholder="شماره موبایل را وارد کنید..."
                dir="ltr"
                style="padding-left:40px;"
            >
            <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);opacity:0.4;pointer-events:none;"
                 width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 01.22 2.18 2 2 0 012.18 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.09a16 16 0 006 6l.56-.56a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/>
            </svg>
        </div>

        {{-- Loading indicator --}}
        <span wire:loading wire:target="searchPhone"
              style="display:inline-block;margin-top:8px;font-size:12px;color:#6B7280;">
            در حال جستجو...
        </span>
    </div>

    {{-- Found patient --}}
    @if($patient)
    <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:12px;padding:16px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div class="avatar"
                 style="background:{{ $patient->avatar_color }}22;color:{{ $patient->avatar_color }};width:44px;height:44px;font-size:16px;">
                {{ $patient->avatar_initial }}
            </div>
            <div style="flex:1;">
                <div style="font-size:15px;font-weight:700;color:#111827;">{{ $patient->full_name }}</div>
                <div style="font-size:12px;color:#6B7280;">
                    <span dir="ltr">{{ $patient->phone }}</span>
                    @if($patient->date_of_birth)
                        <span style="margin:0 6px;">•</span>
                        <span>{{ $patient->age }} ساله</span>
                    @endif
                </div>
            </div>
            <div>
                @php
                    $badgeStyles = [
                        'active'    => 'background:#DCFCE7;color:#15803D',
                        'pending'   => 'background:#FEF9C3;color:#854D0E',
                        'recovered' => 'background:#EEF4FF;color:#1D4ED8',
                        'inactive'  => 'background:#F3F4F6;color:#6B7280',
                    ];
                @endphp
                <span class="badge" style="{{ $badgeStyles[$patient->status] ?? '' }};font-size:11px;">
                    {{ $patient->status_label }}
                </span>
            </div>
        </div>
    </div>

    @elseif($showNewPatientForm)
    {{-- New patient form --}}
    <div style="background:#EEF4FF;border:1px solid #DBEAFE;border-radius:12px;padding:16px;margin-bottom:20px;">
        <p style="font-size:13px;color:#1E40AF;margin:0 0 14px;">
            بیماری با این شماره یافت نشد. اطلاعات را وارد کنید:
        </p>

        <div style="display:grid;gap:12px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label class="field-label">نام <span style="color:#EF4444">*</span></label>
                    <input wire:model="new_first_name" class="form-input" placeholder="نام">
                </div>
                <div>
                    <label class="field-label">نام خانوادگی <span style="color:#EF4444">*</span></label>
                    <input wire:model="new_last_name" class="form-input" placeholder="نام خانوادگی">
                </div>
            </div>
            <div>
                <label class="field-label">موبایل <span style="color:#EF4444">*</span></label>
                <input wire:model="new_phone" class="form-input" placeholder="09123456789" dir="ltr">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label class="field-label">کد ملی</label>
                    <input wire:model="new_national_id" class="form-input" placeholder="0012345678" dir="ltr" maxlength="10">
                </div>
                <div>
                    <label class="field-label">جنسیت</label>
                    <select wire:model="new_gender" class="form-input">
                        <option value="male">مرد</option>
                        <option value="female">زن</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Step 2: Select slot --}}
    @if($patient || $showNewPatientForm)
    <div style="margin-bottom:20px;">
        <label class="field-label">انتخاب ساعت نوبت</label>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;">
            @foreach($suggestedSlots as $slot)
                <button
                    wire:click="selectSlot('{{ $slot['datetime'] }}')"
                    style="padding:12px;border-radius:10px;border:2px solid {{ $selected_slot === $slot['datetime'] ? '#2E5BFF' : '#E5E7EB' }};
                           background:{{ $selected_slot === $slot['datetime'] ? '#EEF4FF' : '#fff' }};
                           cursor:pointer;text-align:center;transition:all 0.15s;">
                    <div style="font-size:15px;font-weight:700;color:{{ $selected_slot === $slot['datetime'] ? '#2E5BFF' : '#111827' }};"
                         dir="ltr">{{ $slot['start'] }}</div>
                    <div style="font-size:11px;color:#6B7280;margin-top:4px;">{{ $slot['date_jalali'] }}</div>
                    @if($loop->first && $slot === $firstAvailable)
                        <div style="font-size:10px;background:#DCFCE7;color:#15803D;padding:2px 6px;border-radius:10px;margin-top:4px;">
                            نزدیک‌ترین
                        </div>
                    @endif
                </button>
            @endforeach
        </div>

        @if($selected_slot)
        <div style="margin-top:12px;">
            <label class="field-label">نوع ویزیت</label>
            <select wire:model="appointment_type" class="form-input" style="width:auto;">
                @foreach(['checkup' => 'معاینه عمومی', 'follow_up' => 'پیگیری', 'consultation' => 'مشاوره'] as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-top:12px;">
            <label class="field-label">توضیحات (اختیاری)</label>
            <input wire:model="appointment_notes" class="form-input" placeholder="توضیحات اضافی...">
        </div>
        @endif
    </div>
    @endif

    {{-- Step 3: Confirm --}}
    @if($patient && $selected_slot)
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <button class="btn-primary" wire:click="createQuickAppointment"
                style="flex:1;justify-content:center;">
            <svg width="14" height="14" style="display:inline;margin-left:6px;vertical-align:-2px"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            ثبت نوبت
        </button>
    </div>
    @endif

    {{-- Alternative: Add to waiting list --}}
    @if(($patient || $showNewPatientForm) && (!$selected_slot || count($suggestedSlots) === 0))
    <div style="border-top:1px solid #F3F4F6;padding-top:16px;margin-top:16px;">
        <button class="btn-ghost" wire:click="addToWaitingList"
                style="width:100%;justify-content:center;background:#FEF9C3;color:#854D0E;">
            <svg width="14" height="14" style="display:inline;margin-left:6px;vertical-align:-2px"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            افزودن به لیست انتظار
        </button>
    </div>
    @endif

    {{-- Loading --}}
    <div wire:loading.delay wire:target="createQuickAppointment,addToWaitingList"
         style="position:fixed;bottom:24px;right:24px;background:#111A6B;color:#fff;
                padding:12px 20px;border-radius:10px;font-size:13px;z-index:500;">
        در حال ذخیره...
    </div>

</div>
