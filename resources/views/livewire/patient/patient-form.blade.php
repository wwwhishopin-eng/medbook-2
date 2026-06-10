<div dir="rtl">
    @if($isOpen)
    <div class="modal-overlay open" style="z-index:250;">
        <div class="modal" style="max-width:520px;">

            {{-- Header --}}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <h3 style="font-size:18px;font-weight:700;color:#111A6B;margin:0;">
                    {{ $patient ? 'ویرایش بیمار' : 'افزودن بیمار جدید' }}
                </h3>
                <button wire:click="closeModal"
                        style="background:none;border:none;cursor:pointer;font-size:20px;color:#9CA3AF;line-height:1;">
                    ✕
                </button>
            </div>

            {{-- Form --}}
            <div style="display:grid;gap:16px;">

                {{-- Name row --}}
                <div class="grid-form-row">
                    <div>
                        <label class="field-label">نام <span style="color:#EF4444">*</span></label>
                        <input wire:model.blur="first_name" class="form-input" placeholder="علی">
                        @error('first_name')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="field-label">نام خانوادگی <span style="color:#EF4444">*</span></label>
                        <input wire:model.blur="last_name" class="form-input" placeholder="محمدی">
                        @error('last_name')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- National ID + DOB --}}
                <div class="grid-form-row">
                    <div>
                        <label class="field-label">کد ملی</label>
                        <input wire:model.blur="national_id" class="form-input" placeholder="0012345678" dir="ltr" maxlength="10">
                        @error('national_id')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="field-label">تاریخ تولد</label>
                        <input wire:model="date_of_birth" type="date" class="form-input" dir="ltr">
                        @error('date_of_birth')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Gender + Blood type --}}
                <div class="grid-form-row">
                    <div>
                        <label class="field-label">جنسیت <span style="color:#EF4444">*</span></label>
                        <select wire:model="gender" class="form-input">
                            <option value="male">مرد</option>
                            <option value="female">زن</option>
                        </select>
                    </div>
                    <div>
                        <label class="field-label">گروه خونی</label>
                        <select wire:model="blood_type" class="form-input">
                            <option value="">انتخاب کنید</option>
                            @foreach($bloodTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Phone + Email --}}
                <div class="grid-form-row">
                    <div>
                        <label class="field-label">شماره موبایل</label>
                        <input wire:model.blur="phone" class="form-input" placeholder="09123456789" dir="ltr">
                        @error('phone')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="field-label">ایمیل</label>
                        <input wire:model.blur="email" type="email" class="form-input" placeholder="ali@example.com" dir="ltr">
                        @error('email')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Conditions --}}
                <div>
                    <label class="field-label">بیماری‌های زمینه‌ای</label>
                    <input wire:model="conditions_raw" class="form-input" placeholder="دیابت، فشار خون، آسم (با ویرگول جدا کنید)">
                    <p style="font-size:11px;color:#9CA3AF;margin:4px 0 0;">هر بیماری را با ویرگول از هم جدا کنید</p>
                </div>

                {{-- Allergies --}}
                <div>
                    <label class="field-label">آلرژی‌ها</label>
                    <input wire:model="allergies_raw" class="form-input" placeholder="پنی‌سیلین، گرده گل (با ویرگول جدا کنید)">
                </div>

                {{-- Emergency contact --}}
                <div style="background:#F9FAFB;border-radius:12px;padding:14px;">
                    <p style="font-size:12px;font-weight:600;color:#6B7280;margin:0 0 12px;">تماس اضطراری</p>
                    <div class="grid-form-row">
                        <div>
                            <label class="field-label">نام</label>
                            <input wire:model="emergency_contact_name" class="form-input" placeholder="فاطمه محمدی">
                        </div>
                        <div>
                            <label class="field-label">موبایل</label>
                            <input wire:model="emergency_contact_phone" class="form-input" placeholder="09120000000" dir="ltr">
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="field-label">وضعیت <span style="color:#EF4444">*</span></label>
                    <select wire:model="status" class="form-input">
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="field-label">یادداشت پزشک</label>
                    <textarea wire:model="notes" class="form-input" rows="3"
                              placeholder="توضیحات اضافی..."></textarea>
                </div>
            </div>

            {{-- Footer buttons --}}
            <div style="display:flex;gap:10px;margin-top:24px;justify-content:flex-end;flex-wrap:wrap;">
                <button class="btn-ghost" wire:click="closeModal" style="flex:1;text-align:center;">انصراف</button>
                <button class="btn-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save" style="flex:1;justify-content:center;">
                    <span wire:loading.remove wire:target="save">
                        {{ $patient ? 'ذخیره تغییرات' : 'ذخیره بیمار' }}
                    </span>
                    <span wire:loading wire:target="save">در حال ذخیره...</span>
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
