<div dir="rtl">
    @if($show && $suggestion)
    <div class="card" style="padding:18px;margin-top:16px;border:2px solid #0E8F72;background:#F0FDF4;">
        <div style="display:flex;align-items:flex-start;gap:14px;flex-wrap:wrap;">
            <div style="width:44px;height:44px;background:#DCFCE7;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0E8F72" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <h4 style="font-size:14px;font-weight:700;color:#065F46;margin:0 0 6px;">
                    پیشنهاد نوبت پیگیری
                </h4>
                <p style="font-size:13px;color:#065F46;margin:0 0 8px;">
                    بر اساس یادداشت پزشک، بیمار نیاز به مراجعه در <strong>{{ $suggestion['label'] }}</strong>
                    ({{ $suggestion['date_jalali'] }} - {{ $suggestion['day_name'] }}) دارد.
                </p>

                @if(isset($suggestion['available_slot']))
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span style="font-size:12px;background:#DCFCE7;color:#15803D;padding:4px 12px;border-radius:20px;">
                        ساعت پیشنهادی: {{ $suggestion['available_slot']['start'] }}
                    </span>

                    <button class="btn-primary" wire:click="bookFollowUp"
                            style="padding:6px 16px;font-size:12px;">
                        ثبت نوبت پیگیری
                    </button>
                    <button style="padding:6px 14px;font-size:12px;background:#F3F4F6;color:#6B7280;border:none;cursor:pointer;border-radius:8px;"
                            wire:click="dismiss">
                        بعداً
                    </button>
                </div>
                @else
                <p style="font-size:12px;color:#6B7280;margin:0 0 8px;">
                    نوبت خالی در این تاریخ یافت نشد. لطفاً از تقویم نوبت‌ها دستی ثبت کنید.
                </p>
                <button style="padding:6px 14px;font-size:12px;background:#F3F4F6;color:#6B7280;border:none;cursor:pointer;border-radius:8px;"
                        wire:click="dismiss">
                    بستن
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
