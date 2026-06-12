<div dir="rtl">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h3 style="font-size:20px;font-weight:700;color:#111A6B;margin:0;">تقویم نوبت‌ها</h3>
        </div>

        {{-- View toggle --}}
        <div class="tab-bar" style="width:auto;">
            @foreach(['daily' => 'روزانه', 'weekly' => 'هفتگی', 'monthly' => 'ماهانه'] as $key => $label)
                <button wire:click="setView('{{ $key }}')"
                        style="font-weight:{{ $view === $key ? '600' : '400' }};
                               {{ $view === $key
                                   ? 'background:linear-gradient(135deg,#2E5BFF,#1A3FDB);color:#fff;box-shadow:0 4px 15px rgba(46,91,255,0.3);'
                                   : 'background:transparent;color:#6B7280;' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Navigation --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;gap:8px;align-items:center;">
            <button wire:click="previousPeriod" class="btn-ghost" style="padding:8px 14px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>
            <span style="font-size:15px;font-weight:700;color:#111A6B;min-width:180px;text-align:center;">{{ $dateLabel }}</span>
            <button wire:click="nextPeriod" class="btn-ghost" style="padding:8px 14px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>
        </div>

        <div style="display:flex;gap:8px;align-items:center;">
            <button wire:click="goToday" class="btn-ghost" style="font-size:12px;padding:6px 14px;">امروز</button>
            <select wire:model.live="statusFilter" class="form-input" style="width:auto;font-size:12px;">
                <option value="">همه وضعیت‌ها</option>
                @foreach(\App\Models\Appointment::STATUSES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Status color legend --}}
    <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
        @foreach([
            'confirmed' => ['رزرو تایید شده', '#DCFCE7', '#15803D'],
            'reserved' => ['در انتظار تایید', '#FEF9C3', '#854D0E'],
            'arrived' => ['حاضر', '#DBEAFE', '#1D4ED8'],
            'completed' => ['انجام شده', '#F0FDF4', '#15803D'],
            'cancelled' => ['لغو شده', '#FEE2E2', '#991B1B'],
            'no_show' => ['غایب', '#F3F4F6', '#6B7280'],
        ] as $status => $info)
            <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:#6B7280;">
                <div style="width:10px;height:10px;border-radius:50%;background:{{ $info[1] }};border:2px solid {{ $info[2] }};"></div>
                {{ $info[0] }}
            </div>
        @endforeach
    </div>

    {{-- Daily View --}}
    @if($view === 'daily')
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="max-height:600px;overflow-y:auto;">
            @foreach($hours as $hour)
                @php
                    $hourAppointments = $appointments->filter(function($a) use ($hour) {
                        return $a->start_at->format('H') === explode(':', $hour)[0];
                    });
                @endphp
                <div style="display:flex;border-bottom:1px solid #F3F4F6;min-height:60px;">
                    {{-- Time label --}}
                    <div style="width:70px;padding:10px;text-align:center;border-left:1px solid #F3F4F6;flex-shrink:0;">
                        <span style="font-size:13px;font-weight:600;color:#6B7280;" dir="ltr">{{ $hour }}</span>
                    </div>
                    {{-- Appointments in this hour --}}
                    <div style="flex:1;padding:8px;display:flex;flex-wrap:wrap;gap:6px;">
                        @foreach($hourAppointments as $appt)
                            <a href="{{ route('patients.show', $appt->patient_id) }}"
                               style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;
                                      text-decoration:none;color:inherit;min-width:180px;flex:1;
                                      background:{{ $appt->status === 'confirmed' ? '#DCFCE7' : ($appt->status === 'reserved' ? '#FEF9C3' : ($appt->status === 'arrived' ? '#DBEAFE' : ($appt->status === 'completed' ? '#F0FDF4' : ($appt->status === 'cancelled' ? '#FEE2E2' : '#F3F4F6'))) }};
                                      border-right:3px solid {{ $appt->status === 'confirmed' ? '#15803D' : ($appt->status === 'reserved' ? '#D97706' : ($appt->status === 'arrived' ? '#1D4ED8' : ($appt->status === 'completed' ? '#15803D' : ($appt->status === 'cancelled' ? '#DC2626' : '#6B7280'))) }};">
                                <div class="avatar"
                                     style="background:{{ $appt->patient->avatar_color }}22;color:{{ $appt->patient->avatar_color }};
                                            width:32px;height:32px;font-size:12px;">
                                    {{ $appt->patient->avatar_initial }}
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:13px;font-weight:600;color:#111827;">{{ $appt->patient->full_name }}</div>
                                    <div style="font-size:11px;color:#6B7280;">{{ $appt->type_label }} • {{ $appt->title }}</div>
                                </div>
                                <span style="font-size:11px;font-weight:600;
                                             color:{{ $appt->status === 'confirmed' ? '#15803D' : ($appt->status === 'reserved' ? '#D97706' : ($appt->status === 'arrived' ? '#1D4ED8' : ($appt->status === 'completed' ? '#15803D' : ($appt->status === 'cancelled' ? '#DC2626' : '#6B7280'))) }};
                                             ">{{ $appt->status_label }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Weekly View --}}
    @elseif($view === 'weekly')
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:700px;">
                <thead>
                    <tr style="background:#F9FAFB;">
                        <th style="padding:10px;font-size:12px;font-weight:600;color:#9CA3AF;text-align:center;width:60px;border-bottom:1px solid #F3F4F6;">ساعت</th>
                        @foreach($days as $day)
                            <th style="padding:10px;font-size:12px;font-weight:600;color:{{ $day['is_today'] ? '#2E5BFF' : '#9CA3AF' }};
                                       text-align:center;border-bottom:1px solid #F3F4F6;
                                       {{ $day['is_today'] ? 'background:#EEF4FF;' : '' }}">
                                <div>{{ $day['day_name'] }}</div>
                                <div style="font-size:11px;">{{ $day['jalali'] }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($hours as $hour)
                        <tr>
                            <td style="padding:8px;text-align:center;border-bottom:1px solid #F9FAFB;font-size:12px;color:#6B7280;" dir="ltr">{{ $hour }}</td>
                            @foreach($days as $day)
                                @php
                                    $cellAppts = $appointments->filter(function($a) use ($day, $hour) {
                                        return $a->start_at->format('Y-m-d') === $day['date']
                                            && $a->start_at->format('H') === explode(':', $hour)[0];
                                    });
                                @endphp
                                <td style="padding:4px;border-bottom:1px solid #F9FAFB;vertical-align:top;
                                           {{ $day['is_today'] ? 'background:#FAFBFF;' : '' }}">
                                    @foreach($cellAppts as $appt)
                                        <a href="{{ route('patients.show', $appt->patient_id) }}"
                                           style="display:block;padding:4px 6px;border-radius:6px;margin-bottom:3px;
                                                  text-decoration:none;font-size:10px;line-height:1.3;
                                                  background:{{ $appt->status === 'confirmed' ? '#DCFCE7' : ($appt->status === 'reserved' ? '#FEF9C3' : ($appt->status === 'arrived' ? '#DBEAFE' : ($appt->status === 'completed' ? '#F0FDF4' : ($appt->status === 'cancelled' ? '#FEE2E2' : '#F3F4F6'))) }};
                                                  border-right:2px solid {{ $appt->status === 'confirmed' ? '#15803D' : ($appt->status === 'reserved' ? '#D97706' : ($appt->status === 'arrived' ? '#1D4ED8' : ($appt->status === 'cancelled' ? '#DC2626' : '#6B7280'))) }};
                                                  color:#111827;">
                                            {{ $appt->patient->full_name }}
                                            <div style="color:#6B7280;" dir="ltr">{{ $appt->start_at->format('H:i') }}</div>
                                        </a>
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Monthly View --}}
    @else
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:700px;">
                <thead>
                    <tr style="background:#F9FAFB;">
                        @foreach(['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه'] as $dayName)
                            <th style="padding:10px;font-size:12px;font-weight:600;color:#9CA3AF;text-align:center;border-bottom:1px solid #F3F4F6;">
                                {{ $dayName }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($days->chunk(7) as $week)
                        <tr>
                            @foreach($week as $day)
                                @php
                                    $dayAppts = $appointments->filter(fn($a) => $a->start_at->format('Y-m-d') === $day['date']);
                                @endphp
                                <td style="padding:6px;min-height:80px;vertical-align:top;
                                           border:1px solid #F3F4F6;
                                           {{ !$day['is_current_month'] ? 'background:#F9FAFB;opacity:0.5;' : '' }}
                                           {{ $day['is_today'] ? 'background:#EEF4FF;' : '' }}">
                                    <div style="font-size:12px;font-weight:{{ $day['is_today'] ? '700' : '500' }};
                                                color:{{ $day['is_today'] ? '#2E5BFF' : '#111827' }};
                                                margin-bottom:4px;">
                                        @fa($day['jalali'])
                                    </div>
                                    @foreach($dayAppts->take(3) as $appt)
                                        <a href="{{ route('patients.show', $appt->patient_id) }}"
                                           style="display:block;padding:2px 6px;border-radius:4px;margin-bottom:2px;
                                                  text-decoration:none;font-size:10px;line-height:1.3;
                                                  background:{{ $appt->status === 'confirmed' ? '#DCFCE7' : ($appt->status === 'reserved' ? '#FEF9C3' : ($appt->status === 'arrived' ? '#DBEAFE' : ($appt->status === 'cancelled' ? '#FEE2E2' : '#F3F4F6'))) }};
                                                  color:#111827;">
                                            <span dir="ltr">{{ $appt->start_at->format('H:i') }}</span>
                                            {{ $appt->patient->avatar_initial }} {{ $appt->patient->full_name }}
                                        </a>
                                    @endforeach
                                    @if($dayAppts->count() > 3)
                                        <div style="font-size:10px;color:#6B7280;padding:2px 6px;">+@fa($dayAppts->count() - 3) دیگر</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div wire:loading.delay wire:target="previousPeriod,nextPeriod,setView"
         style="position:fixed;bottom:24px;right:24px;background:#111A6B;color:#fff;
                padding:12px 20px;border-radius:10px;font-size:13px;z-index:500;">
        در حال بارگذاری...
    </div>
</div>
