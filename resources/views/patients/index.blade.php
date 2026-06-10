<x-layouts.app title="بیماران">

    {{-- Page header --}}
    <div class="page-container" dir="rtl">
        <div class="page-inner">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
                <div>
                    <h2 style="font-size:22px;font-weight:800;color:#111A6B;margin:0;">لیست بیماران</h2>
                    <p style="font-size:13px;color:#9CA3AF;margin:4px 0 0;">
                        مدیریت و جستجو در پرونده بیماران
                    </p>
                </div>

                {{-- Stats pills --}}
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    @php
                        $total   = \App\Models\Patient::count();
                        $active  = \App\Models\Patient::where('status','active')->count();
                        $pending = \App\Models\Patient::where('status','pending')->count();
                    @endphp

                    <div style="background:#fff;border-radius:10px;padding:8px 16px;box-shadow:0 1px 4px rgba(17,26,107,0.07);
                                display:flex;align-items:center;gap:8px;">
                        <span style="font-size:18px;font-weight:800;color:#111A6B;">@fa($total)</span>
                        <span style="font-size:12px;color:#9CA3AF;">کل بیماران</span>
                    </div>

                    <div style="background:#DCFCE7;border-radius:10px;padding:8px 16px;
                                display:flex;align-items:center;gap:8px;">
                        <span style="font-size:18px;font-weight:800;color:#15803D;">@fa($active)</span>
                        <span style="font-size:12px;color:#15803D;">فعال</span>
                    </div>

                    <div style="background:#FEF9C3;border-radius:10px;padding:8px 16px;
                                display:flex;align-items:center;gap:8px;">
                        <span style="font-size:18px;font-weight:800;color:#854D0E;">@fa($pending)</span>
                        <span style="font-size:12px;color:#854D0E;">در انتظار</span>
                    </div>
                </div>
            </div>

            {{-- Livewire list component (search + table + delete modal) --}}
            @livewire('patient.patient-list')

            {{-- Patient form modal (listens for 'open-patient-form' Livewire events) --}}
            @livewire('patient.patient-form')
        </div>
    </div>

</x-layouts.app>
