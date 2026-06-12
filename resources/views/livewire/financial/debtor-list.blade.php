<div dir="rtl">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h3 style="font-size:20px;font-weight:700;color:#111A6B;margin:0;">لیست بدهکاران</h3>
            <p style="font-size:12px;color:#6B7280;margin:4px 0 0;">بیمارانی که بدهی معوق دارند</p>
        </div>
        <div style="background:#FEE2E2;border-radius:12px;padding:12px 20px;display:flex;align-items:center;gap:10px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
            <div>
                <div style="font-size:12px;color:#991B1B;">مجموع بدهی</div>
                <div style="font-size:18px;font-weight:800;color:#991B1B;">@faCurrency($totalDebt)</div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
        <div style="position:relative;flex:1;min-width:200px;">
            <input wire:model.live.debounce.300ms="search" class="form-input"
                   placeholder="جستجو با نام، کد ملی یا موبایل...">
        </div>
        <select wire:model.live="sortBy" class="form-input" style="width:auto;">
            <option value="debt_desc">بیشترین بدهی</option>
            <option value="debt_asc">کمترین بدهی</option>
            <option value="name_asc">نام (الفبا)</option>
        </select>
    </div>

    {{-- Debtor cards --}}
    @forelse($debtors as $debtor)
    <div class="card" style="padding:18px;margin-bottom:12px;">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <a href="{{ route('patients.show', $debtor) }}"
               style="display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit;flex:1;min-width:200px;">
                <div class="avatar"
                     style="background:{{ $debtor->avatar_color }}22;color:{{ $debtor->avatar_color }};
                            width:48px;height:48px;font-size:18px;">
                    {{ $debtor->avatar_initial }}
                </div>
                <div>
                    <div style="font-size:15px;font-weight:700;color:#111827;">{{ $debtor->full_name }}</div>
                    <div style="font-size:12px;color:#9CA3AF;">
                        <span dir="ltr">{{ $debtor->phone }}</span>
                        @if($debtor->phone)
                            <span style="margin:0 6px;">•</span>
                        @endif
                        <span dir="ltr">{{ $debtor->code }}</span>
                    </div>
                </div>
            </a>

            <div style="text-align:center;min-width:100px;">
                <div style="font-size:22px;font-weight:800;color:#DC2626;">@faCurrency($debtor->debt)</div>
                <div style="font-size:11px;color:#9CA3AF;">بدهی معوق</div>
            </div>

            <div style="display:flex;gap:8px;">
                <a href="{{ route('patients.transactions', $debtor) }}"
                   style="padding:8px 14px;border-radius:8px;background:#EEF4FF;color:#2E5BFF;
                          font-size:12px;text-decoration:none;font-weight:600;">
                    تراکنش‌ها
                </a>
                <button wire:click="sendDebtReminder({{ $debtor->id }})"
                        style="padding:8px 14px;border-radius:8px;background:#FEF9C3;color:#854D0E;
                               font-size:12px;border:none;cursor:pointer;font-weight:600;">
                    پیامک یادآوری
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5"
             style="margin:0 auto 12px;display:block;">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        <p style="font-size:14px;color:#9CA3AF;margin:0;">
            @if($search)
                هیچ بدهکاری با این جستجو یافت نشد.
            @else
                بدهکار فعلی وجود ندارد.
            @endif
        </p>
    </div>
    @endforelse

    <div wire:loading.delay wire:target="sendDebtReminder"
         style="position:fixed;bottom:24px;right:24px;background:#111A6B;color:#fff;
                padding:12px 20px;border-radius:10px;font-size:13px;z-index:500;">
        در حال ارسال...
    </div>
</div>
