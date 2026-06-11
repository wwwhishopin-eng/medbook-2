<x-layouts.app title="ثبت سریع نوبت">

    <div class="page-container" dir="rtl">
        <div class="page-inner" style="max-width:640px;">

            {{-- Breadcrumb --}}
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#9CA3AF;margin-bottom:20px;">
                <a href="{{ route('dashboard') }}"
                   style="color:#2E5BFF;text-decoration:none;font-weight:500;">داشبورد</a>
                <span>/</span>
                <span style="color:#111827;">ثبت سریع نوبت</span>
            </div>

            {{-- Quick Booking Component --}}
            @livewire('appointment.quick-booking')

        </div>
    </div>

</x-layouts.app>
