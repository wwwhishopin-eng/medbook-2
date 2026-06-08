<x-layouts.app title="افزودن بیمار">

    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#9CA3AF;margin-bottom:20px;">
        <a href="{{ route('patients.index') }}"
           style="color:#2E5BFF;text-decoration:none;font-weight:500;">بیماران</a>
        <span>/</span>
        <span>افزودن بیمار جدید</span>
    </div>

    <div class="card" style="max-width:640px;padding:32px;">
        <h2 style="font-size:18px;font-weight:700;color:#111A6B;margin:0 0 24px;">اطلاعات بیمار</h2>

        <form method="POST" action="{{ route('patients.store') }}">
            @csrf

            <div style="display:grid;gap:18px;">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">
                            نام <span style="color:#EF4444">*</span>
                        </label>
                        <input name="first_name" value="{{ old('first_name') }}" class="form-input">
                        @error('first_name')
                            <p style="font-size:11px;color:#EF4444;margin:4px 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">
                            نام خانوادگی <span style="color:#EF4444">*</span>
                        </label>
                        <input name="last_name" value="{{ old('last_name') }}" class="form-input">
                        @error('last_name')
                            <p style="font-size:11px;color:#EF4444;margin:4px 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">کد ملی</label>
                        <input name="national_id" value="{{ old('national_id') }}" class="form-input" dir="ltr" maxlength="10">
                        @error('national_id')
                            <p style="font-size:11px;color:#EF4444;margin:4px 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">تاریخ تولد</label>
                        <input name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" class="form-input" dir="ltr">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">جنسیت <span style="color:#EF4444">*</span></label>
                        <select name="gender" class="form-input">
                            <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>مرد</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>زن</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">گروه خونی</label>
                        <select name="blood_type" class="form-input">
                            <option value="">انتخاب کنید</option>
                            @foreach($bloodTypes as $type)
                                <option value="{{ $type }}" {{ old('blood_type') === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">موبایل</label>
                        <input name="phone" value="{{ old('phone') }}" class="form-input" dir="ltr">
                        @error('phone')
                            <p style="font-size:11px;color:#EF4444;margin:4px 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">ایمیل</label>
                        <input name="email" type="email" value="{{ old('email') }}" class="form-input" dir="ltr">
                        @error('email')
                            <p style="font-size:11px;color:#EF4444;margin:4px 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">آدرس</label>
                    <textarea name="address" class="form-input" rows="2">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">وضعیت <span style="color:#EF4444">*</span></label>
                    <select name="status" class="form-input" style="width:auto;">
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ old('status', 'pending') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">یادداشت</label>
                    <textarea name="notes" class="form-input" rows="3" placeholder="توضیحات اضافی...">{{ old('notes') }}</textarea>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                    <a href="{{ route('patients.index') }}" class="btn-ghost">انصراف</a>
                    <button type="submit" class="btn-primary">ذخیره بیمار</button>
                </div>
            </div>
        </form>
    </div>

</x-layouts.app>