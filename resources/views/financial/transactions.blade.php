<x-layouts.app title="تراکنش‌های بیمار">

    <div class="page-container" dir="rtl">
        <div class="page-inner">

            {{-- Breadcrumb --}}
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#9CA3AF;margin-bottom:20px;">
                <a href="{{ route('patients.index') }}"
                   style="color:#2E5BFF;text-decoration:none;font-weight:500;">بیماران</a>
                <span>/</span>
                <a href="{{ route('patients.show', $patient) }}"
                   style="color:#2E5BFF;text-decoration:none;font-weight:500;">{{ $patient->full_name }}</a>
                <span>/</span>
                <span style="color:#111827;">تراکنش‌ها</span>
            </div>

            @livewire('financial.patient-transactions', ['patient' => $patient], key('txn-'.$patient->id))

        </div>
    </div>

</x-layouts.app>
