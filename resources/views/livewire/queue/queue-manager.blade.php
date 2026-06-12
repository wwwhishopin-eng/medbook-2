<div dir="rtl">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h3 style="font-size:20px;font-weight:700;color:#111A6B;margin:0;">مدیریت صف</h3>
            <p style="font-size:12px;color:#6B7280;margin:4px 0 0;">{{ $dateJalali }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <input wire:model.live="selectedDate" type="date" class="form-input" style="width:auto;" dir="ltr">
            <button class="btn-primary" wire:click="callNext">
                <svg width="14" height="14" style="display:inline;margin-left:5px;vertical-align:-2px"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13"/>
                </svg>
                فراخوان بعدی
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:24px;">
        <div class="card" style="padding:16px;text-align:center;">
            <div style="font-size:28px;font-weight:800;color:#1D4ED8;">@fa($arrived->count())</div>
            <div style="font-size:12px;color:#6B7280;">حاضر در مطب</div>
        </div>
        <div class="card" style="padding:16px;text-align:center;">
            <div style="font-size:28px;font-weight:800;color:#D97706;">@fa($waiting->count())</div>
            <div style="font-size:12px;color:#6B7280;">در انتظار</div>
        </div>
        <div class="card" style="padding:16px;text-align:center;">
            <div style="font-size:28px;font-weight:800;color:#15803D;">@fa($completed->count())</div>
            <div style="font-size:12px;color:#6B7280;">تکمیل شده</div>
        </div>
    </div>

    {{-- Current patient --}}
    @if($currentPatient)
    <div class="card" style="padding:20px;margin-bottom:20px;border:2px solid #1D4ED8;background:#F0F5FF;">
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="width:56px;height:56px;background:#1D4ED8;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div style="flex:1;">
                <div style="font-size:12px;color:#1D4ED8;font-weight:600;">در حال ویزیت</div>
                <div style="font-size:20px;font-weight:800;color:#111A6B;">{{ $currentPatient->patient->full_name }}</div>
                <div style="font-size:13px;color:#6B7280;">
                    {{ $currentPatient->title }} • {{ $currentPatient->type_label }}
                    <span dir="ltr" style="margin-right:8px;">{{ $currentPatient->start_at->format('H:i') }}</span>
                </div>
            </div>
            <button wire:click="markAsCompleted({{ $currentPatient->id }})"
                    class="btn-primary" style="background:linear-gradient(135deg,#15803D,#0E8F72);">
                تکمیل ویزیت
            </button>
        </div>
    </div>
    @endif

    {{-- Queue list --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:14px 18px;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;justify-content:space-between;">
            <h4 style="font-size:14px;font-weight:700;color:#111A6B;margin:0;">صف انتظار</h4>
            <span style="font-size:12px;color:#9CA3AF;">@fa($appointments->count()) نوبت</span>
        </div>

        @forelse($appointments as $index => $appt)
        <div style="display:flex;align-items:center;gap:14px;padding:14px 18px;border-bottom:1px solid #F3F4F6;
                    {{ $appt->id === $currentPatient?->id ? 'background:#F0F5FF;' : '' }}">

            {{-- Queue position --}}
            <div style="width:32px;text-align:center;flex-shrink:0;">
                <span style="font-size:16px;font-weight:700;color:#2E5BFF;">@fa($index + 1)</span>
            </div>

            {{-- Avatar + info --}}
            <div class="avatar"
                 style="background:{{ $appt->patient->avatar_color }}22;color:{{ $appt->patient->avatar_color }};
                        width:40px;height:40px;font-size:14px;">
                {{ $appt->patient->avatar_initial }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:600;color:#111827;">{{ $appt->patient->full_name }}</div>
                <div style="font-size:12px;color:#6B7280;">
                    {{ $appt->type_label }}
                    <span dir="ltr" style="margin-right:6px;">{{ $appt->start_at->format('H:i') }}</span>
                </div>
            </div>

            {{-- Status badge --}}
            <span class="badge" style="{{ $appt->status_badge_style }};font-size:11px;">
                {{ $appt->status_label }}
            </span>

            {{-- Actions --}}
            <div style="display:flex;gap:6px;">
                @if(in_array($appt->status, [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED]))
                    <button wire:click="markAsArrived({{ $appt->id }})"
                            style="padding:6px 12px;border-radius:8px;background:#DBEAFE;color:#1D4ED8;
                                   font-size:11px;border:none;cursor:pointer;font-weight:600;">
                        حاضر شد
                    </button>
                @endif

                @if($appt->status === Appointment::STATUS_ARRIVED && (!$currentPatient || $appt->id !== $currentPatient->id))
                    <button wire:click="markAsCompleted({{ $appt->id }})"
                            style="padding:6px 12px;border-radius:8px;background:#DCFCE7;color:#15803D;
                                   font-size:11px;border:none;cursor:pointer;font-weight:600;">
                        تکمیل
                    </button>
                @endif

                @if(in_array($appt->status, [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED, Appointment::STATUS_ARRIVED]))
                    <button wire:click="markAsNoShow({{ $appt->id }})"
                            style="padding:6px 12px;border-radius:8px;background:#F3F4F6;color:#6B7280;
                                   font-size:11px;border:none;cursor:pointer;font-weight:600;">
                        غایب
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div style="padding:40px;text-align:center;color:#9CA3AF;">
            نوبتی برای این تاریخ ثبت نشده است.
        </div>
        @endforelse
    </div>

    <div wire:loading.delay wire:target="markAsArrived,markAsCompleted,markAsNoShow,callNext"
         style="position:fixed;bottom:24px;right:24px;background:#111A6B;color:#fff;
                padding:12px 20px;border-radius:10px;font-size:13px;z-index:500;">
        در حال پردازش...
    </div>
</div>
