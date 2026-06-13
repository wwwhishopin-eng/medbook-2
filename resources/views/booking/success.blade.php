<x-layouts.guest>
    <div dir="rtl" style="max-width:460px;margin:0 auto;width:100%;text-align:center;">
        <div style="width:72px;height:72px;background:#DCFCE7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>

        <h1 style="font-size:22px;font-weight:800;color:#111A6B;margin:0 0 8px;">
            نوبت شما ثبت شد!
        </h1>

        <p style="font-size:14px;color:#6B7280;margin:0 0 24px;line-height:1.8;">
            درخواست شما با موفقیت ثبت شد. مطب پس از بررسی نوبت شما را تایید خواهد کرد و از طریق پیامک نتیجه را اطلاع می‌دهد.
        </p>

        <a href="{{ route('booking.show', $bookingSlot->slug) }}" class="btn-primary" style="display:inline-flex;text-decoration:none;padding:10px 24px;">
            رزرو نوبت دیگر
        </a>
    </div>
</x-layouts.guest>
