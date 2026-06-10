<x-layouts.app :title="$patient->full_name">

    <div class="page-container" dir="rtl">
        <div class="page-inner">
            {{-- Breadcrumb --}}
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#9CA3AF;margin-bottom:20px;">
                <a href="{{ route('patients.index') }}"
                   style="color:#2E5BFF;text-decoration:none;font-weight:500;">بیماران</a>
                <span>/</span>
                <span style="color:#111827;">{{ $patient->full_name }}</span>
            </div>

            {{-- Livewire profile component (tabs: overview, history, conditions, contact) --}}
            @livewire('patient.patient-profile', ['patient' => $patient], key('profile-'.$patient->id))

            {{-- Patient form modal (listens for 'open-patient-form' Livewire events) --}}
            @livewire('patient.patient-form')
        </div>
    </div>

</x-layouts.app>
