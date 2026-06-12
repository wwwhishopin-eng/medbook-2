<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size:22px;font-weight:800;color:#111A6B;margin:0;font-family:'Vazirmatn',sans-serif;">
            داشبورد
        </h2>
    </x-slot>

    <div class="page-container" dir="rtl">
        <div class="page-inner">

            {{-- Stats Cards --}}
            @php
                $totalPatients   = \App\Models\Patient::count();
                $activePatients  = \App\Models\Patient::where('status', 'active')->count();
                $pendingPatients = \App\Models\Patient::where('status', 'pending')->count();
                $upcomingAppointments = \App\Models\Appointment::upcoming()->count();
                $todayAppointments = \App\Models\Appointment::today()->count();
                $todayCompleted = \App\Models\Appointment::today()->where('status', 'completed')->count();
                $arrivedToday = \App\Models\Appointment::today()->where('status', 'arrived')->count();
                $totalDebt = \App\Models\Transaction::getTotalDebt();
                $debtorCount = \App\Models\Patient::whereHas('transactions')->get()->filter(fn($p) => $p->debt > 0)->count();
                $recentPatients = \App\Models\Patient::latest()->limit(5)->get();
                $upcomingVisits = \App\Models\Appointment::with('patient')
                    ->upcoming()
                    ->orderBy('start_at')
                    ->limit(5)
                    ->get();
            @endphp

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">

                {{-- Total Patients --}}
                <div class="card" style="padding:20px;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#EEF4FF,#DBEAFE);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2E5BFF" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:28px;font-weight:800;color:#111A6B;">@fa($totalPatients)</div>
                            <div style="font-size:12px;color:#9CA3AF;">کل بیماران</div>
                        </div>
                    </div>
                </div>

                {{-- Active --}}
                <div class="card" style="padding:20px;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#DCFCE7,#BBF7D0);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:28px;font-weight:800;color:#15803D;">@fa($activePatients)</div>
                            <div style="font-size:12px;color:#9CA3AF;">فعال</div>
                        </div>
                    </div>
                </div>

                {{-- Pending --}}
                <div class="card" style="padding:20px;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#FEF9C3,#FDE68A);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#854D0E" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:28px;font-weight:800;color:#854D0E;">@fa($pendingPatients)</div>
                            <div style="font-size:12px;color:#9CA3AF;">در انتظار</div>
                        </div>
                    </div>
                </div>

                {{-- Upcoming Appointments --}}
                <div class="card" style="padding:20px;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#EEF4FF,#E0E7FF);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2E5BFF" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:28px;font-weight:800;color:#2E5BFF;">@fa($upcomingAppointments)</div>
                            <div style="font-size:12px;color:#9CA3AF;">نوبت آینده</div>
                        </div>
                    </div>
                </div>

                {{-- Today's Appointments --}}
                <div class="card" style="padding:20px;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#F0FDF4,#DCFCE7);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0E8F72" stroke-width="2">
                                <path d="M9 11l3 3L22 4"/>
                                <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:28px;font-weight:800;color:#0E8F72;">@fa($todayAppointments)</div>
                            <div style="font-size:12px;color:#9CA3AF;">نوبت امروز</div>
                        </div>
                    </div>
                </div>

                {{-- Arrived Today --}}
                <div class="card" style="padding:20px;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#DBEAFE,#BFDBFE);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1D4ED8" stroke-width="2">
                                <path d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:28px;font-weight:800;color:#1D4ED8;">@fa($arrivedToday)</div>
                            <div style="font-size:12px;color:#9CA3AF;">حاضر در مطب</div>
                        </div>
                    </div>
                </div>

                {{-- Total Debt --}}
                <a href="{{ route('financial.debtors') }}" style="text-decoration:none;">
                <div class="card" style="padding:20px;{{ $totalDebt > 0 ? 'border:2px solid #FECACA;' : '' }}">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#FEE2E2,#FECACA);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:28px;font-weight:800;color:#DC2626;">@faCurrency($totalDebt)</div>
                            <div style="font-size:12px;color:#9CA3AF;">بدهی معوق (@fa($debtorCount) بدهکار)</div>
                        </div>
                    </div>
                </div>
                </a>
            </div>

            {{-- Two-column: Recent Patients + Upcoming Appointments --}}
            <div class="dashboard-grid">

                {{-- Recent Patients --}}
                <div class="card" style="padding:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                        <h3 style="font-size:15px;font-weight:700;color:#111A6B;margin:0;">آخرین بیماران</h3>
                        <a href="{{ route('patients.index') }}" style="font-size:12px;color:#2E5BFF;text-decoration:none;">مشاهده همه</a>
                    </div>
                    @forelse($recentPatients as $patient)
                        <a href="{{ route('patients.show', $patient) }}"
                           style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #F3F4F6;text-decoration:none;color:inherit;">
                            <div class="avatar"
                                 style="background:{{ $patient->avatar_color }}22;color:{{ $patient->avatar_color }};
                                        width:36px;height:36px;font-size:14px;">
                                {{ $patient->avatar_initial }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:13px;font-weight:600;color:#111827;">{{ $patient->full_name }}</div>
                                <div style="font-size:11px;color:#9CA3AF;" dir="ltr">{{ $patient->code }}</div>
                            </div>
                            @php
                                $badgeStyles = [
                                    'active'    => 'background:#DCFCE7;color:#15803D',
                                    'pending'   => 'background:#FEF9C3;color:#854D0E',
                                    'recovered' => 'background:#EEF4FF;color:#1D4ED8',
                                    'inactive'  => 'background:#F3F4F6;color:#6B7280',
                                ];
                            @endphp
                            <span class="badge" style="{{ $badgeStyles[$patient->status] ?? '' }};font-size:11px;">
                                {{ $patient->status_label }}
                            </span>
                        </a>
                    @empty
                        <p style="text-align:center;color:#9CA3AF;padding:24px 0;font-size:13px;">هنوز بیماری ثبت نشده است.</p>
                    @endforelse
                </div>

                {{-- Upcoming Appointments --}}
                <div class="card" style="padding:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                        <h3 style="font-size:15px;font-weight:700;color:#111A6B;margin:0;">نوبت‌های آینده</h3>
                    </div>
                    @forelse($upcomingVisits as $appt)
                        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #F3F4F6;">
                            <div style="width:48px;text-align:center;flex-shrink:0;">
                                <div style="font-size:18px;font-weight:700;color:#2E5BFF;">{{ $appt->start_at->format('d') }}</div>
                                <div style="font-size:11px;color:#9CA3AF;">{{ $appt->start_at->format('M') }}</div>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:13px;font-weight:600;color:#111827;">{{ $appt->title }}</div>
                                <div style="font-size:11px;color:#9CA3AF;">
                                    {{ $appt->patient->full_name }}
                                    <span dir="ltr" style="margin-right:6px;">{{ $appt->start_at->format('H:i') }}</span>
                                </div>
                            </div>
                            <span class="badge" style="{{ $appt->status_badge_style }};font-size:11px;">
                                {{ $appt->status_label }}
                            </span>
                            <span class="badge" style="background:#EEF4FF;color:#2E5BFF;font-size:11px;">
                                {{ $appt->type_label }}
                            </span>
                        </div>
                    @empty
                        <p style="text-align:center;color:#9CA3AF;padding:24px 0;font-size:13px;">نوبت آینده‌ای ثبت نشده.</p>
                    @endforelse
                </div>
            </div>

            {{-- Quick Actions --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-top:24px;">
                <a href="{{ route('appointments.quick') }}" class="card" style="padding:16px;text-align:center;text-decoration:none;transition:transform 0.15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2E5BFF" stroke-width="2" style="margin:0 auto 8px;display:block;">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <div style="font-size:13px;font-weight:600;color:#111827;">ثبت نوبت</div>
                </a>
                <a href="{{ route('appointments.calendar') }}" class="card" style="padding:16px;text-align:center;text-decoration:none;transition:transform 0.15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0E8F72" stroke-width="2" style="margin:0 auto 8px;display:block;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <div style="font-size:13px;font-weight:600;color:#111827;">تقویم</div>
                </a>
                <a href="{{ route('queue.index') }}" class="card" style="padding:16px;text-align:center;text-decoration:none;transition:transform 0.15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1D4ED8" stroke-width="2" style="margin:0 auto 8px;display:block;">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <div style="font-size:13px;font-weight:600;color:#111827;">مدیریت صف</div>
                </a>
                <a href="{{ route('financial.debtors') }}" class="card" style="padding:16px;text-align:center;text-decoration:none;transition:transform 0.15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" style="margin:0 auto 8px;display:block;">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                    </svg>
                    <div style="font-size:13px;font-weight:600;color:#111827;">بدهکاران</div>
                </a>
                <a href="{{ route('waiting-room.display') }}" class="card" style="padding:16px;text-align:center;text-decoration:none;transition:transform 0.15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2" style="margin:0 auto 8px;display:block;">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                    <div style="font-size:13px;font-weight:600;color:#111827;">صف نمایش</div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
