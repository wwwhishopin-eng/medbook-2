<x-layouts.app title="سابقه پزشکی">

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
                <span style="color:#111827;">سابقه پزشکی</span>
            </div>

            {{-- Patient mini-header --}}
            <div class="card" style="padding:16px 20px;margin-bottom:20px;
                                     display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                <div class="avatar"
                     style="background:{{ $patient->avatar_color }}22;color:{{ $patient->avatar_color }};
                            width:48px;height:48px;font-size:18px;">
                    {{ $patient->avatar_initial }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:16px;font-weight:700;color:#111A6B;">{{ $patient->full_name }}</div>
                    <div style="font-size:12px;color:#9CA3AF;" dir="ltr">{{ $patient->code }}</div>
                </div>
                <a href="{{ route('patients.show', $patient) }}"
                   class="btn-ghost" style="font-size:12px;padding:7px 14px;">
                    برگشت به پرونده
                </a>
            </div>

            {{-- Livewire history component --}}
            @livewire('patient.patient-history', ['patient' => $patient], key('hist-'.$patient->id))
        </div>
    </div>

</x-layouts.app>
