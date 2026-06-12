<div dir="rtl">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h3 style="font-size:18px;font-weight:700;color:#111A6B;margin:0;">
                @if($patient)
                    تراکنش‌های {{ $patient->full_name }}
                @else
                    تراکنش‌های مالی
                @endif
            </h3>
            @if($patient && $patient->has_debt)
                <div style="display:flex;align-items:center;gap:8px;margin-top:6px;">
                    <span style="background:#FEE2E2;color:#991B1B;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                        بدهی: @faCurrency($patient->debt)
                    </span>
                </div>
            @endif
        </div>
        <button class="btn-primary" wire:click="openForm">
            <svg width="13" height="13" style="display:inline;margin-left:5px;vertical-align:-1px"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            ثبت تراکنش
        </button>
    </div>

    {{-- Add transaction form --}}
    @if($showForm)
    <div class="card" style="padding:20px;margin-bottom:20px;border:2px solid #EEF4FF;">
        <h4 style="font-size:15px;font-weight:700;color:#111A6B;margin:0 0 16px;">ثبت تراکنش جدید</h4>
        <div style="display:grid;gap:14px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label class="field-label">نوع تراکنش <span style="color:#EF4444">*</span></label>
                    <select wire:model="form_type" class="form-input">
                        <option value="charge">هزینه</option>
                        <option value="payment">پرداخت</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">مبلغ (تومان) <span style="color:#EF4444">*</span></label>
                    <input wire:model="form_amount" class="form-input" placeholder="مبلغ" dir="ltr" type="number">
                    @error('form_amount') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="field-label">شرح <span style="color:#EF4444">*</span></label>
                <input wire:model="form_description" class="form-input" placeholder="ویزیت، آزمایش، دارو...">
                @error('form_description') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="grid-form-row">
                <div>
                    <label class="field-label">تاریخ <span style="color:#EF4444">*</span></label>
                    <input wire:model="form_date" type="date" class="form-input" dir="ltr">
                    @error('form_date') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label">توضیحات</label>
                    <input wire:model="form_notes" class="form-input" placeholder="اختیاری">
                </div>
            </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:18px;justify-content:flex-end;">
            <button class="btn-ghost" wire:click="$set('showForm', false)">انصراف</button>
            <button class="btn-primary" wire:click="saveTransaction">ذخیره تراکنش</button>
        </div>
    </div>
    @endif

    {{-- Filters --}}
    @if(!$patient)
    <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
        <div style="position:relative;flex:1;min-width:200px;">
            <input wire:model.live.debounce.300ms="search" class="form-input"
                   placeholder="جستجو بیمار...">
        </div>
        <select wire:model.live="typeFilter" class="form-input" style="width:auto;">
            <option value="">همه انواع</option>
            <option value="charge">هزینه</option>
            <option value="payment">پرداخت</option>
        </select>
    </div>
    @endif

    {{-- Transaction list --}}
    @forelse($transactions as $txn)
    <div class="card" style="padding:14px 18px;margin-bottom:8px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
        <div style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                    background:{{ $txn->type === 'charge' ? '#FEE2E2' : '#DCFCE7' }};">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="{{ $txn->type === 'charge' ? '#DC2626' : '#15803D' }}" stroke-width="2">
                @if($txn->type === 'charge')
                    <path d="M12 5v14M5 12h14"/>
                @else
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                @endif
            </svg>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:14px;font-weight:600;color:#111827;">{{ $txn->description }}</div>
            @if(!$patient && $txn->patient)
                <div style="font-size:12px;color:#6B7280;">{{ $txn->patient->full_name }}</div>
            @endif
        </div>
        <div style="text-align:left;">
            <div style="font-size:16px;font-weight:700;color:{{ $txn->type === 'charge' ? '#DC2626' : '#15803D' }};">
                @if($txn->type === 'charge')-@else+@endif @faCurrency($txn->amount)
            </div>
            <div style="font-size:11px;color:#9CA3AF;">{{ JalaliDate::format($txn->transaction_date, 'Y/m/d') }}</div>
        </div>
    </div>
    @empty
    <div class="card" style="padding:40px;text-align:center;">
        <p style="font-size:14px;color:#9CA3AF;margin:0;">تراکنشی ثبت نشده است.</p>
    </div>
    @endforelse

    @if($transactions->hasPages())
        <div style="margin-top:16px;">{{ $transactions->links() }}</div>
    @endif

    <div wire:loading.delay wire:target="saveTransaction"
         style="position:fixed;bottom:24px;right:24px;background:#111A6B;color:#fff;
                padding:12px 20px;border-radius:10px;font-size:13px;z-index:500;">
        در حال ذخیره...
    </div>
</div>
