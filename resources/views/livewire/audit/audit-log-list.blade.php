<div dir="rtl">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h3 style="font-size:20px;font-weight:700;color:#111A6B;margin:0;">لاگ فعالیت‌ها</h3>
            <p style="font-size:12px;color:#6B7280;margin:4px 0 0;">تاریخچه عملیات کاربران در سیستم</p>
        </div>
    </div>

    {{-- Filters --}}
    <div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
        <div style="position:relative;flex:1;min-width:180px;">
            <input wire:model.live.debounce.300ms="search" class="form-input" placeholder="جستجو کاربر...">
        </div>
        <select wire:model.live="actionFilter" class="form-input" style="width:auto;">
            <option value="">همه عملیات</option>
            @foreach(\App\Models\AuditLog::ACTIONS as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
        <select wire:model.live="modelFilter" class="form-input" style="width:auto;">
            <option value="">همه مدل‌ها</option>
            @foreach($modelTypes as $class => $name)
                <option value="{{ $class }}">{{ $name }}</option>
            @endforeach
        </select>
        <input wire:model.live="dateFrom" type="date" class="form-input" style="width:auto;" dir="ltr" placeholder="از تاریخ">
        <input wire:model.live="dateTo" type="date" class="form-input" style="width:auto;" dir="ltr" placeholder="تا تاریخ">
    </div>

    {{-- Log list --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div class="table-wrapper">
            <table class="data-table" style="width:100%">
                <thead>
                    <tr>
                        <th style="text-align:right;">زمان</th>
                        <th style="text-align:right;">کاربر</th>
                        <th style="text-align:right;">عملیات</th>
                        <th style="text-align:right;">مدل</th>
                        <th style="text-align:right;">IP</th>
                        <th style="text-align:right;">تغییرات</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="font-size:12px;white-space:nowrap;">
                            {{ \App\Helpers\JalaliDate::format($log->created_at, 'Y/m/d H:i') }}
                        </td>
                        <td>
                            @if($log->user)
                                <span style="font-weight:600;color:#111827;">{{ $log->user->name }}</span>
                            @else
                                <span style="color:#9CA3AF;">سیستم</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $actionStyles = [
                                    'create' => 'background:#DCFCE7;color:#15803D',
                                    'update' => 'background:#EEF4FF;color:#1D4ED8',
                                    'delete' => 'background:#FEE2E2;color:#991B1B',
                                    'restore' => 'background:#FEF9C3;color:#854D0E',
                                    'login' => 'background:#F0FDF4;color:#15803D',
                                    'logout' => 'background:#F3F4F6;color:#6B7280',
                                ];
                            @endphp
                            <span class="badge" style="{{ $actionStyles[$log->action] ?? 'background:#F3F4F6;color:#6B7280' }};font-size:11px;">
                                {{ $log->action_label }}
                            </span>
                        </td>
                        <td style="font-size:12px;">
                            @if($log->model_type)
                                <span>{{ class_basename($log->model_type) }}</span>
                                @if($log->model_id)
                                    <span style="color:#9CA3AF;"> #{{ $log->model_id }}</span>
                                @endif
                            @else
                                <span style="color:#D1D5DB;">—</span>
                            @endif
                        </td>
                        <td style="font-size:11px;color:#9CA3AF;" dir="ltr">{{ $log->ip_address }}</td>
                        <td style="font-size:11px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            @if($log->new_values)
                                <span title="{{ json_encode($log->new_values, JSON_UNESCAPED_UNICODE) }}" style="cursor:help;">
                                    {{ collect($log->new_values)->keys()->take(3)->implode(', ') }}{{ collect($log->new_values)->count() > 3 ? '...' : '' }}
                                </span>
                            @else
                                <span style="color:#D1D5DB;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:#9CA3AF;">
                            هیچ لاگی یافت نشد.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div style="padding:16px 20px;border-top:1px solid #F3F4F6;">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
