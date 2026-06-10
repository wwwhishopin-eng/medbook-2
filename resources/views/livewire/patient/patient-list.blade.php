<div dir="rtl">
    {{-- Search & Filter Bar --}}
    <div class="search-bar" style="margin-bottom:20px;">

        {{-- Search --}}
        <div style="position:relative;flex:1;min-width:200px;">
            <input
                wire:model.live.debounce.300ms="search"
                class="form-input"
                placeholder="جستجو با نام، کد ملی یا موبایل..."
                autocomplete="off"
            >
            <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);opacity:0.4"
                 width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </div>

        {{-- Status filter --}}
        <select wire:model.live="statusFilter" class="form-input" style="width:auto;">
            <option value="">همه وضعیت‌ها</option>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>

        {{-- Add patient button --}}
        <button class="btn-primary" wire:click="$dispatch('open-patient-form')">
            <svg width="14" height="14" style="display:inline;margin-left:6px;vertical-align:-2px"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            افزودن بیمار
        </button>
    </div>

    {{-- Mobile: Card view --}}
    @forelse($patients as $patient)
    <div class="patient-card md:hidden">
        <div class="patient-card-header">
            <a href="{{ route('patients.show', $patient) }}"
               style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;flex:1;">
                <div class="avatar"
                     style="background:{{ $patient->avatar_color }}22;color:{{ $patient->avatar_color }};">
                    {{ $patient->avatar_initial }}
                </div>
                <div>
                    <div style="font-weight:600;color:#111827;">{{ $patient->full_name }}</div>
                    <div style="font-size:11px;color:#9CA3AF;direction:ltr">{{ $patient->code }}</div>
                </div>
            </a>
            @php
                $badgeStyles = [
                    'active'    => 'background:#DCFCE7;color:#15803D',
                    'pending'   => 'background:#FEF9C3;color:#854D0E',
                    'recovered' => 'background:#EEF4FF;color:#1D4ED8',
                    'inactive'  => 'background:#F3F4F6;color:#6B7280',
                ];
            @endphp
            <span class="badge" style="{{ $badgeStyles[$patient->status] ?? '' }}">
                {{ $patient->status_label }}
            </span>
        </div>

        <div class="patient-card-meta">
            @if($patient->date_of_birth)
            <span class="patient-card-meta-item">{{ $patient->age }} ساله</span>
            @endif
            @if($patient->conditions && count($patient->conditions))
            <span class="patient-card-meta-item">{{ implode('، ', array_slice($patient->conditions, 0, 2)) }}</span>
            @endif
            @if($patient->phone)
            <span class="patient-card-meta-item" dir="ltr">{{ $patient->phone }}</span>
            @endif
        </div>

        <div class="patient-card-actions">
            <a href="{{ route('patients.show', $patient) }}"
               style="padding:6px 14px;border-radius:8px;background:#EEF4FF;color:#2E5BFF;font-size:12px;text-decoration:none;flex:1;text-align:center;">
                پرونده
            </a>
            <button
                wire:click="$dispatch('open-patient-form', { id: {{ $patient->id }} })"
                style="padding:6px 14px;border-radius:8px;background:#F3F4F6;color:#374151;font-size:12px;border:none;cursor:pointer;flex:1;">
                ویرایش
            </button>
            <button
                wire:click="confirmDelete({{ $patient->id }})"
                style="padding:6px 14px;border-radius:8px;background:#FEE2E2;color:#991B1B;font-size:12px;border:none;cursor:pointer;flex:1;">
                حذف
            </button>
        </div>
    </div>
    @empty
    <div class="patient-card md:hidden" style="text-align:center;padding:32px;color:#9CA3AF;">
        @if($search || $statusFilter)
            هیچ بیماری با این فیلترها یافت نشد.
        @else
            هنوز بیماری ثبت نشده است.
        @endif
    </div>
    @endforelse

    {{-- Desktop: Table view --}}
    <div class="card hidden md:block" style="padding:0;overflow:hidden;">
        <div class="table-wrapper">
            <table class="data-table" style="width:100%">
                <thead>
                <tr>
                    <th style="text-align:right;">#</th>
                    <th style="text-align:right;cursor:pointer;" wire:click="sortBy('first_name')">
                        بیمار
                        @if($sortField === 'first_name')
                            <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th style="text-align:right;cursor:pointer;" wire:click="sortBy('date_of_birth')">سن</th>
                    <th style="text-align:right;">بیماری زمینه‌ای</th>
                    <th style="text-align:right;">موبایل</th>
                    <th style="text-align:right;cursor:pointer;" wire:click="sortBy('status')">
                        وضعیت
                        @if($sortField === 'status')
                            <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th style="text-align:right;">عملیات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td style="color:#9CA3AF;font-size:12px;">
                            {{ $patients->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <a href="{{ route('patients.show', $patient) }}"
                               style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
                                <div class="avatar"
                                     style="background:{{ $patient->avatar_color }}22;color:{{ $patient->avatar_color }};">
                                    {{ $patient->avatar_initial }}
                                </div>
                                <div>
                                    <div style="font-weight:600;color:#111827;">{{ $patient->full_name }}</div>
                                    <div style="font-size:11px;color:#9CA3AF;direction:ltr">{{ $patient->code }}</div>
                                </div>
                            </a>
                        </td>
                        <td>{{ $patient->date_of_birth ? $patient->age . ' سال' : '—' }}</td>
                        <td style="max-width:180px;">
                            @if($patient->conditions)
                                <span title="{{ implode('، ', $patient->conditions) }}"
                                      style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;max-width:160px;">
                                    {{ implode('، ', $patient->conditions) }}
                                </span>
                            @else
                                <span style="color:#D1D5DB;">—</span>
                            @endif
                        </td>
                        <td dir="ltr" style="font-size:13px;">{{ $patient->phone ?? '—' }}</td>
                        <td>
                            @php
                                $badgeStyles = [
                                    'active'    => 'background:#DCFCE7;color:#15803D',
                                    'pending'   => 'background:#FEF9C3;color:#854D0E',
                                    'recovered' => 'background:#EEF4FF;color:#1D4ED8',
                                    'inactive'  => 'background:#F3F4F6;color:#6B7280',
                                ];
                            @endphp
                            <span class="badge" style="{{ $badgeStyles[$patient->status] ?? '' }}">
                                {{ $patient->status_label }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('patients.show', $patient) }}"
                                   style="padding:5px 10px;border-radius:8px;background:#EEF4FF;color:#2E5BFF;font-size:12px;text-decoration:none;">
                                    پرونده
                                </a>
                                <button
                                    wire:click="$dispatch('open-patient-form', { id: {{ $patient->id }} })"
                                    style="padding:5px 10px;border-radius:8px;background:#F3F4F6;color:#374151;font-size:12px;border:none;cursor:pointer;">
                                    ویرایش
                                </button>
                                <button
                                    wire:click="confirmDelete({{ $patient->id }})"
                                    style="padding:5px 10px;border-radius:8px;background:#FEE2E2;color:#991B1B;font-size:12px;border:none;cursor:pointer;">
                                    حذف
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#9CA3AF;">
                            @if($search || $statusFilter)
                                هیچ بیماری با این فیلترها یافت نشد.
                            @else
                                هنوز بیماری ثبت نشده است.
                            @endif
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($patients->hasPages())
            <div style="padding:16px 20px;border-top:1px solid #F3F4F6;">
                {{ $patients->links() }}
            </div>
        @endif
    </div>

    {{-- Mobile pagination --}}
    @if($patients->hasPages())
        <div class="md:hidden" style="margin-top:12px;">
            {{ $patients->links() }}
        </div>
    @endif

    {{-- Delete confirmation modal --}}
    @if($confirmingDelete)
        <div class="modal-overlay open" style="z-index:300;">
            <div class="modal" style="max-width:380px;text-align:center;">
                <div style="width:56px;height:56px;background:#FEE2E2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/>
                    </svg>
                </div>
                <h3 style="font-size:17px;font-weight:700;color:#111A6B;margin:0 0 8px;">حذف بیمار</h3>
                <p style="font-size:14px;color:#6B7280;margin:0 0 24px;">
                    آیا مطمئنید؟ این عملیات قابل بازگشت است (حذف نرم).
                </p>
                <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                    <button class="btn-ghost" wire:click="cancelDelete">انصراف</button>
                    <button class="btn-primary" wire:click="deletePatient"
                            style="background:linear-gradient(135deg,#DC2626,#B91C1C);">
                        بله، حذف شود
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Loading indicator --}}
    <div wire:loading.delay wire:target="search,statusFilter,sortBy,deletePatient"
         style="position:fixed;bottom:24px;right:24px;background:#111A6B;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;z-index:500;">
        در حال بارگذاری...
    </div>
</div>
