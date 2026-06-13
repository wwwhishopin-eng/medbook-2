<x-layouts.guest>
    <div dir="rtl" style="max-width:540px;margin:0 auto;width:100%;">
        <div style="text-align:center;margin-bottom:32px;">
            <div style="width:64px;height:64px;background:linear-gradient(135deg,#2E5BFF,#1A3FDB);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <h1 style="font-size:22px;font-weight:800;color:#111A6B;margin:0 0 4px;">
                رزرو نوبت آنلاین
            </h1>
            <p style="font-size:14px;color:#6B7280;margin:0;">
                مطب {{ $doctor->name }}
            </p>
        </div>

        @if(session('error'))
        <div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:12px;padding:14px;margin-bottom:20px;">
            <p style="font-size:13px;color:#991B1B;margin:0;">{{ session('error') }}</p>
        </div>
        @endif

        <form method="POST" action="{{ route('booking.book', $bookingSlot->slug) }}" style="display:grid;gap:16px;">
            @csrf

            <div>
                <label class="field-label">نام و نام خانوادگی <span style="color:#EF4444">*</span></label>
                <input name="patient_name" value="{{ old('patient_name') }}" class="form-input" placeholder="نام و نام خانوادگی" required>
                @error('patient_name') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label class="field-label">شماره موبایل <span style="color:#EF4444">*</span></label>
                    <input name="patient_phone" value="{{ old('patient_phone') }}" class="form-input" placeholder="09123456789" dir="ltr" required>
                    @error('patient_phone') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label">کد ملی</label>
                    <input name="patient_national_id" value="{{ old('patient_national_id') }}" class="form-input" placeholder="0012345678" dir="ltr" maxlength="10">
                </div>
            </div>

            <div>
                <label class="field-label">تاریخ و ساعت نوبت <span style="color:#EF4444">*</span></label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <input type="date" id="booking-date" class="form-input" dir="ltr" min="{{ now()->addDay()->format('Y-m-d') }}" onchange="loadSlots(this.value)">
                    <select name="start_at" id="booking-slots" class="form-input" required>
                        <option value="">ابتدا تاریخ را انتخاب کنید</option>
                    </select>
                </div>
                @error('start_at') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label">توضیحات (اختیاری)</label>
                <textarea name="notes" class="form-input" rows="2" placeholder="دلیل مراجعه یا توضیحات اضافی...">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:12px;">
                ثبت رزرو نوبت
            </button>
        </form>

        <p style="font-size:11px;color:#9CA3AF;text-align:center;margin-top:20px;">
            نوبت شما پس از تایید مطب نهایی می‌شود.
        </p>
    </div>

    <script>
        async function loadSlots(date) {
            const sel = document.getElementById('booking-slots');
            sel.innerHTML = '<option value="">در حال بارگذاری...</option>';

            try {
                const res = await fetch('{{ url("/booking/" . $bookingSlot->slug . "/slots") }}?date=' + date);
                const slots = await res.json();

                if (slots.length === 0) {
                    sel.innerHTML = '<option value="">نوبت خالی وجود ندارد</option>';
                    return;
                }

                sel.innerHTML = '<option value="">انتخاب ساعت</option>';
                slots.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.datetime;
                    opt.textContent = s.start + ' - ' + s.end + '  (' + s.date_jalali + ')';
                    sel.appendChild(opt);
                });
            } catch (e) {
                sel.innerHTML = '<option value="">خطا در بارگذاری</option>';
            }
        }
    </script>
</x-layouts.guest>
