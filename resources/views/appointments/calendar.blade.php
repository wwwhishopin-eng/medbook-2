<x-layouts.app title="تقویم نوبت‌ها">

    <div class="page-container" dir="rtl">
        <div class="page-inner">

            {{-- Breadcrumb --}}
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#9CA3AF;margin-bottom:20px;">
                <a href="{{ route('dashboard') }}"
                   style="color:#2E5BFF;text-decoration:none;font-weight:500;">داشبورد</a>
                <span>/</span>
                <span style="color:#111827;">تقویم نوبت‌ها</span>
            </div>

            @livewire('appointment.appointment-calendar')

        </div>
    </div>

</x-layouts.app>
