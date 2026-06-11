<div dir="rtl">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h3 style="font-size:18px;font-weight:700;color:#111A6B;margin:0;">
                لیست انتظار
            </h3>
            <p style="font-size:12px;color:#6B7280;margin:4px 0 0;">
                بیمارانی که منتظر نوبت خالی هستند
            </p>
        </div>
    </div>

    {{-- Filters --}}
    <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
        <div style="position:relative;flex:1;min-width:200px;">
            <input
                wire:model.live.debounce.300ms="search"
                class="form-input"
                placeholder="جستجو بیمار..."
            >
            <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);opacity:0.4"
                 width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </div>

        <select wire:model.live="statusFilter" class="form-input" style="width:auto;">
            <option value="">همه وضعیت‌ها</option>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- List --}}
    @forelse($waitingList as $waiting)
        <div class="card" style="padding:16px;margin-bottom:12px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">

            {{-- Patient info --}}
            <a href="{{ route('patients.show', $waiting->patient) }}"
               style="display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit;flex:1;min-width:200px;">
                <div class="avatar"
                     style="background:{{ $waiting->patient->avatar_color }}22;color:{{ $waiting->patient->avatar_color }};
                            width:44px;height:44px;font-size:16px;">
                    {{ $waiting->patient->avatar_initial }}
                </div>
                <div>
                    <div style="font-weight:600;color:#111827;">{{ $waiting->patient->full_name }}</div>
                    <div style="font-size:12px;color:#6B7280;">
                        <span dir="ltr">{{ $waiting->patient->phone }}</span>
                    </div>
                </div>
            </a>

            {{-- Preferred time --}}
            @if($waiting->preferred_date)
            <div style="text-align:center;min-width:80px;">
                <div style="font-size:12px;color:#9CA3AF;">تاریخ ترجیحی</div>
                <div style="font-size:13px;font-weight:600;">
                    {{ \App\Helpers\JalaliDate::format($waiting->preferred_date, 'Y/m/d') }}
                </div>
            </div>
            @endif

            {{-- Status --}}
            @php
                $statusStyles = [
                    'waiting'   => 'background:#FEF9C3;color:#854D0E',
                    'notified'  => 'background:#EEF4FF;color:#1D4ED8',
                    'assigned'  => 'background:#DCFCE7;color:#15803D',
                    'cancelled' => 'background:#FEE2E2;color:#991B1B',
                ];
            @endphp
            <span class="badge" style="{{ $statusStyles[$waiting->status] ?? '' }}">
                {{ $waiting->status_label }}
            </span>

            {{-- Created --}}
            <span style="font-size:11px;color:#9CA3AF;">
                {{ \App\Helpers\JalaliDate::format($waiting->created_at, 'Y/m/d') }}
            </span>

            {{-- Actions --}}
            @if($waiting->status === 'waiting')
            <div style="display:flex;gap:6px;">
                <button
                    wire:click="openAssignModal('{{ $waiting->id }}')"
                    style="padding:6px 12px;border-radius:8px;background:#2E5BFF;color:#fff;
                           font-size:12px;border:none;cursor:pointer;">
                    ثبت نوبت
                </button>
                <button
                    wire:click="notifyWaiting('{{ $waiting->id }}')"
                    style="padding:6px 12px;border-radius:8px;background:#EEF4FF;color:#2E5BFF;
                           font-size:12px;border:none;cursor:pointer;">
                    ارسال پیامک
                </button>
                <button
                    wire:click="cancelWaiting('{{ $waiting->id }}')"
                    style="padding:6px 12px;border-radius:8px;background:#FEE2E2;color:#991B1B;
                           font-size:12px;border:none;cursor:pointer;">
                    لغو
                </button>
            </div>
            @endif
        </div>
    @empty
        <div class="card" style="padding:40px;text-align:center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5"
                 style="margin:0 auto 12px;display:block;">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p style="font-size:14px;color:#9CA3AF;margin:0;">
                @if($statusFilter || $search)
                    هیچ نتیجه‌ای یافت نشد.
                @else
                    لیست انتظار خالی است.
                @endif
            </p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($waitingList->hasPages())
        <div style="margin-top:16px;">{{ $waitingList->links() }}</div>
    @endif

    {{-- Assign Modal --}}
    @if($showAssignModal)
        <div class="modal-overlay open" style="z-index:250;">
            <div class="modal" style="max-width:420px;">
                <h4 style="font-size:16px;font-weight:700;color:#111A6B;margin:0 0 16px;">
                    ثبت نوبت از لیست انتظار
                </h4>

                <div style="margin-bottom:16px;">
                    <label class="field-label">انتخاب ساعت نوبت</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;max-height:300px;overflow-y:auto;padding:4px;">
                        @foreach($availableSlots as $slot)
                            <button
                                wire:click="$set('selectedSlot', '{{ $slot['datetime'] }}')"
                                style="padding:8px;border-radius:8px;
                                       border:2px solid {{ $selectedSlot === $slot['datetime'] ? '#2E5BFF' : '#E5E7EB' }};
                                       background:{{ $selectedSlot === $slot['datetime'] ? '#EEF4FF' : '#fff' }};
                                       cursor:pointer;text-align:center;">
                                <div style="font-size:12px;font-weight:600;" dir="ltr">{{ $slot['start'] }}</div>
                                <div style="font-size:10px;color:#6B7280;">{{ $slot['date_jalali'] }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div style="display:flex;gap:10px;">
                    <button class="btn-ghost" wire:click="$set('showAssignModal', false)" style="flex:1;">
                        انصراف
                    </button>
                    <button class="btn-primary" wire:click="assignAppointment" style="flex:1;">
                        ثبت نوبت
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div wire:loading.delay wire:target="assignAppointment,notifyWaiting,cancelWaiting"
         style="position:fixed;bottom:24px;right:24px;background:#111A6B;color:#fff;
                padding:12px 20px;border-radius:10px;font-size:13px;z-index:500;">
        در حال پردازش...
    </div>
</div>
