<div dir="rtl" style="position:relative;" x-data @click.outside="$wire.close()">

    {{-- Search input --}}
    <div style="position:relative;">
        <input
            wire:model.live.debounce.250ms="query"
            class="form-input"
            placeholder="جستجوی سریع بیمار..."
            autocomplete="off"
            style="padding-left:40px;"
        >
        {{-- Search icon --}}
        <svg style="position:absolute;left:13px;top:50%;transform:translateY(-50%);opacity:0.4;pointer-events:none;"
             width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        {{-- Loading spinner --}}
        <span wire:loading wire:target="query"
              style="position:absolute;left:13px;top:50%;transform:translateY(-50%);width:15px;height:15px;
                     border:2px solid #E5E7EB;border-top-color:#2E5BFF;border-radius:50%;
                     animation:spin .6s linear infinite;display:inline-block;">
        </span>
    </div>

    {{-- Dropdown results --}}
    @if($isOpen && count($results))
        <div style="position:absolute;top:calc(100% + 6px);right:0;left:0;background:#fff;border-radius:12px;
                    box-shadow:0 8px 30px rgba(17,26,107,0.15);border:1px solid #E5E7EB;z-index:400;overflow:hidden;">

            <p style="font-size:11px;font-weight:600;color:#9CA3AF;padding:10px 14px 6px;
                      text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #F3F4F6;">
                نتایج جستجو
            </p>

            @foreach($results as $result)
                <a href="{{ $result['url'] }}"
                   style="display:flex;align-items:center;gap:10px;padding:10px 14px;
                          text-decoration:none;color:inherit;transition:background .15s;"
                   onmouseover="this.style.background='#F8F9FF'"
                   onmouseout="this.style.background=''"
                >
                    {{-- Avatar --}}
                    <div class="avatar"
                         style="background:{{ $result['color'] }}22;color:{{ $result['color'] }};
                                width:34px;height:34px;font-size:13px;flex-shrink:0;">
                        {{ mb_substr($result['name'], 0, 1) }}
                    </div>

                    {{-- Name + code --}}
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:#111827;">
                            {{ $result['name'] }}
                        </div>
                        <div style="font-size:11px;color:#9CA3AF;display:flex;gap:8px;">
                            <span dir="ltr">{{ $result['code'] }}</span>
                            @if($result['phone'])
                                <span dir="ltr">• {{ $result['phone'] }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Status badge --}}
                    @php
                        $colors = [
                            'فعال'          => 'background:#DCFCE7;color:#15803D',
                            'در انتظار'     => 'background:#FEF9C3;color:#854D0E',
                            'بهبودیافته'    => 'background:#EEF4FF;color:#1D4ED8',
                            'غیرفعال'       => 'background:#F3F4F6;color:#6B7280',
                        ];
                    @endphp
                    <span class="badge" style="{{ $colors[$result['status']] ?? '' }};font-size:11px;">
                        {{ $result['status'] }}
                    </span>
                </a>
            @endforeach

            <div style="padding:10px 14px;border-top:1px solid #F3F4F6;">
                <a href="{{ route('patients.index') }}?q={{ urlencode($query) }}"
                   style="font-size:12px;color:#2E5BFF;text-decoration:none;">
                    مشاهده همه نتایج →
                </a>
            </div>
        </div>
    @endif

    <style>
        @keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }
    </style>
</div>