<div dir="rtl">

    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;">

        {{-- Visit type filter --}}
        <select wire:model.live="visitTypeFilter" class="form-input" style="width:auto;">
            <option value="">همه انواع ویزیت</option>
            @foreach($visitTypes as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>

        {{-- Date range --}}
        <input wire:model.live="dateFrom" type="date" class="form-input"
               style="width:auto;" dir="ltr" placeholder="از تاریخ">
        <input wire:model.live="dateTo" type="date" class="form-input"
               style="width:auto;" dir="ltr" placeholder="تا تاریخ">

        {{-- Add entry button --}}
        <button class="btn-primary" wire:click="openForm" style="margin-right:auto;">
            <svg width="13" height="13" style="display:inline;margin-left:5px;vertical-align:-1px"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            ثبت مراجعه
        </button>
    </div>

    {{-- Add entry form --}}
    @if($showForm)
    <div class="card" style="padding:20px;margin-bottom:20px;border:2px solid #EEF4FF;" class="fade-up">
        <h4 style="font-size:15px;font-weight:700;color:#111A6B;margin:0 0 16px;">ثبت مراجعه جدید</h4>

        <div style="display:grid;gap:14px;">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="field-label">تاریخ ویزیت <span style="color:#EF4444">*</span></label>
                    <input wire:model="visit_date" type="date" class="form-input" dir="ltr">
                    @error('visit_date') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label">نوع ویزیت</label>
                    <select wire:model="visit_type" class="form-input">
                        @foreach($visitTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="field-label">شکایت اصلی</label>
                <input wire:model="chief_complaint" class="form-input" placeholder="دلیل مراجعه بیمار...">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="field-label">تشخیص</label>
                    <textarea wire:model="diagnosis" class="form-input" rows="3"
                              placeholder="تشخیص پزشک..."></textarea>
                </div>
                <div>
                    <label class="field-label">درمان</label>
                    <textarea wire:model="treatment" class="form-input" rows="3"
                              placeholder="روش درمان..."></textarea>
                </div>
            </div>

            <div>
                <label class="field-label">نسخه / داروها</label>
                <textarea wire:model="prescriptions" class="form-input" rows="2"
                          placeholder="نام داروها، دوز، مدت..."></textarea>
            </div>

            <div>
                <label class="field-label">یادداشت پزشک</label>
                <textarea wire:model="doctor_notes" class="form-input" rows="2"
                          placeholder="توضیحات اضافی..."></textarea>
            </div>

            <div>
                <label class="field-label">تاریخ پیگیری</label>
                <input wire:model="follow_up_date" type="date" class="form-input"
                       style="width:220px;" dir="ltr">
                @error('follow_up_date') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:18px;justify-content:flex-end;">
            <button class="btn-ghost" wire:click="$set('showForm', false)">انصراف</button>
            <button class="btn-primary" wire:click="saveEntry" wire:loading.attr="disabled" wire:target="saveEntry">
                <span wire:loading.remove wire:target="saveEntry">ذخیره مراجعه</span>
                <span wire:loading wire:target="saveEntry">در حال ذخیره...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- History timeline --}}
    @forelse($history as $entry)
        <div class="card" style="padding:20px;margin-bottom:12px;position:relative;overflow:hidden;">

            {{-- Color accent strip --}}
            <div style="position:absolute;right:0;top:0;bottom:0;width:4px;
                        background:{{ ['first_visit'=>'#2E5BFF','follow_up'=>'#0E8F72','lab_review'=>'#9333EA','consultation'=>'#EA580C','emergency'=>'#E11D48'][$entry->visit_type] ?? '#2E5BFF' }};
                        border-radius:0 4px 4px 0;">
            </div>

            <div style="padding-right:12px;">
                {{-- Header row --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:14px;font-weight:700;color:#111A6B;">
                            {{ $entry->visit_type_label }}
                        </span>
                        @if($entry->chief_complaint)
                            <span style="font-size:12px;color:#6B7280;">— {{ $entry->chief_complaint }}</span>
                        @endif
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        @if($entry->follow_up_date)
                            <span style="font-size:11px;background:#FEF9C3;color:#854D0E;padding:3px 10px;border-radius:20px;">
                                پیگیری: {{ $entry->follow_up_date->format('Y/m/d') }}
                            </span>
                        @endif
                        <span style="font-size:12px;color:#9CA3AF;">
                            {{ $entry->visit_date->format('Y/m/d') }}
                        </span>
                        @if($entry->doctor)
                            <span style="font-size:11px;color:#9CA3AF;">| {{ $entry->doctor->name }}</span>
                        @endif
                    </div>
                </div>

                {{-- Content grid --}}
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
                    @if($entry->diagnosis)
                    <div style="background:#F9FAFB;border-radius:10px;padding:12px;">
                        <p style="font-size:11px;font-weight:600;color:#9CA3AF;margin:0 0 6px;text-transform:uppercase;">تشخیص</p>
                        <p style="font-size:13px;color:#111827;margin:0;line-height:1.6;">{{ $entry->diagnosis }}</p>
                    </div>
                    @endif

                    @if($entry->treatment)
                    <div style="background:#F9FAFB;border-radius:10px;padding:12px;">
                        <p style="font-size:11px;font-weight:600;color:#9CA3AF;margin:0 0 6px;text-transform:uppercase;">درمان</p>
                        <p style="font-size:13px;color:#111827;margin:0;line-height:1.6;">{{ $entry->treatment }}</p>
                    </div>
                    @endif

                    @if($entry->prescriptions)
                    <div style="background:#EDFAF6;border-radius:10px;padding:12px;">
                        <p style="font-size:11px;font-weight:600;color:#0E8F72;margin:0 0 6px;text-transform:uppercase;">نسخه</p>
                        <p style="font-size:13px;color:#065047;margin:0;line-height:1.6;">{{ $entry->prescriptions }}</p>
                    </div>
                    @endif

                    @if($entry->doctor_notes)
                    <div style="background:#F9FAFB;border-radius:10px;padding:12px;">
                        <p style="font-size:11px;font-weight:600;color:#9CA3AF;margin:0 0 6px;text-transform:uppercase;">یادداشت</p>
                        <p style="font-size:13px;color:#374151;margin:0;line-height:1.6;">{{ $entry->doctor_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="padding:48px;text-align:center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5"
                 style="margin:0 auto 12px;display:block;">
                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p style="font-size:14px;color:#9CA3AF;margin:0;">سابقه پزشکی برای این بیمار ثبت نشده است.</p>
            <button class="btn-primary" wire:click="openForm" style="margin-top:16px;">ثبت اولین مراجعه</button>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($history->hasPages())
        <div style="margin-top:16px;">{{ $history->links() }}</div>
    @endif

    {{-- Loading overlay --}}
    <div wire:loading.delay wire:target="visitTypeFilter,dateFrom,dateTo,saveEntry"
         style="position:fixed;bottom:24px;right:24px;background:#111A6B;color:#fff;
                padding:12px 20px;border-radius:10px;font-size:13px;z-index:500;">
        در حال بارگذاری...
    </div>

    <style>
        .field-label { display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px; }
        .field-error { font-size:11px;color:#EF4444;margin:4px 0 0; }
    </style>
</div>