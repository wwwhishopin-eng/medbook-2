<div dir="rtl" style="min-height:100vh;background:linear-gradient(135deg,#0F172A,#1E293B);color:#fff;padding:40px 24px;">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:40px;flex-wrap:wrap;gap:16px;">
        <div>
            <h1 style="font-size:28px;font-weight:800;margin:0;">اتاق انتظار</h1>
            <p style="font-size:14px;color:#94A3B8;margin:4px 0 0;">{{ $dateJalali }}</p>
        </div>
        <div style="font-size:32px;font-weight:800;color:#60A5FA;" dir="ltr">{{ $time }}</div>
    </div>

    {{-- Current patient --}}
    @if($currentPatient)
    <div style="background:linear-gradient(135deg,#1D4ED8,#2563EB);border-radius:20px;padding:32px;margin-bottom:32px;
                box-shadow:0 8px 32px rgba(29,78,216,0.3);">
        <div style="font-size:12px;color:#BFDBFE;font-weight:600;margin-bottom:8px;">نفر بعدی</div>
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <div style="width:72px;height:72px;background:rgba(255,255,255,0.2);border-radius:18px;display:flex;align-items:center;justify-content:center;
                        font-size:28px;font-weight:800;">
                {{ $currentPatient->patient->avatar_initial }}
            </div>
            <div>
                <div style="font-size:32px;font-weight:800;">{{ $currentPatient->patient->full_name }}</div>
                <div style="font-size:16px;color:#BFDBFE;">
                    کد @fa($currentPatient->patient->code)
                    <span style="margin:0 12px;">•</span>
                    اتاق پزشک
                </div>
            </div>
        </div>
    </div>
    @else
    <div style="background:rgba(255,255,255,0.05);border-radius:20px;padding:40px;margin-bottom:32px;text-align:center;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="1.5"
             style="margin:0 auto 12px;display:block;">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
        </svg>
        <div style="font-size:18px;color:#64748B;">در حال حاضر بیماری در ویزیت نیست</div>
    </div>
    @endif

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:16px;margin-bottom:32px;">
        <div style="background:rgba(255,255,255,0.08);border-radius:14px;padding:20px;text-align:center;">
            <div style="font-size:28px;font-weight:800;color:#60A5FA;">@fa($waitingCount)</div>
            <div style="font-size:12px;color:#94A3B8;">در انتظار</div>
        </div>
        <div style="background:rgba(255,255,255,0.08);border-radius:14px;padding:20px;text-align:center;">
            <div style="font-size:28px;font-weight:800;color:#34D399;">@fa($completedCount)</div>
            <div style="font-size:12px;color:#94A3B8;">تکمیل شده</div>
        </div>
    </div>

    {{-- Upcoming list --}}
    @if($upcoming->count())
    <div style="background:rgba(255,255,255,0.05);border-radius:16px;padding:20px;">
        <h3 style="font-size:14px;font-weight:600;color:#94A3B8;margin:0 0 16px;">نوبت‌های بعدی</h3>
        @foreach($upcoming as $appt)
        <div style="display:flex;align-items:center;gap:14px;padding:12px 0;{{ !$loop->last ? 'border-bottom:1px solid rgba(255,255,255,0.08);' : '' }}">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.1);border-radius:12px;
                        display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;">
                {{ $appt->patient->avatar_initial }}
            </div>
            <div style="flex:1;">
                <div style="font-size:15px;font-weight:600;">{{ $appt->patient->full_name }}</div>
                <div style="font-size:12px;color:#94A3B8;">
                    {{ $appt->type_label }}
                    <span dir="ltr" style="margin-right:6px;">{{ $appt->start_at->format('H:i') }}</span>
                </div>
            </div>
            <span style="font-size:12px;color:#94A3B8;">نفر @fa($loop->index + 1)</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Auto-refresh hint --}}
    <div style="margin-top:32px;text-align:center;font-size:11px;color:#475569;">
        صفحه هر ۳۰ ثانیه به‌روزرسانی می‌شود
    </div>

    <script>
        setTimeout(() => {
            Livewire.dispatch('$refresh');
        }, 30000);
    </script>
</div>
