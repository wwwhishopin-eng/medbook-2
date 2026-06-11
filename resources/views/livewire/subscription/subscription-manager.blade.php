<div dir="rtl">
    <div class="page-container">
        <div class="page-inner" style="max-width:960px;">

            <section class="card" style="padding:24px;margin-bottom:20px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:20px;">
                    <div>
                        <h2 style="font-size:20px;font-weight:700;color:#111A6B;margin:0 0 6px;">
                            مدیریت اشتراک
                        </h2>
                        <p style="font-size:13px;color:#6B7280;margin:0;">
                            وضعیت و تمدید اشتراک سیستم مدیریت مطب
                        </p>
                    </div>

                    <div style="text-align:left;">
                        <span class="badge" style="{{ $this->getStatusBadgeStyle() }};font-size:13px;">
                            {{ $expStatus['status_label'] ?? '—' }}
                        </span>
                    </div>
                </div>

                @if($expStatus)
                @if($expStatus['expired'])
                <div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:12px;padding:16px;margin-bottom:20px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <div>
                            <h4 style="font-size:14px;font-weight:700;color:#991B1B;margin:0;">اشتراک منقضی شده</h4>
                            <p style="font-size:13px;color:#7F1D1D;margin:4px 0 0;">لطفاً برای ادامه استفاده از سیستم، اشتراک خود را تمدید کنید.</p>
                        </div>
                    </div>
                </div>
                @elseif($expStatus['critical'])
                <div style="background:#FEF9C3;border:1px solid #FDE68A;border-radius:12px;padding:16px;margin-bottom:20px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        <div>
                            <h4 style="font-size:14px;font-weight:700;color:#92400E;margin:0;">هدرد!</h4>
                            <p style="font-size:13px;color:#78350F;margin:4px 0 0;">
                               تنها <strong>@fa($expStatus['days_remaining'])</strong> روز تا انقضای اشتراک باقی مانده!
                            </p>
                        </div>
                    </div>
                </div>
                @elseif($expStatus['near_expiration'])
                <div style="background:#EEF4FF;border:1px solid #DBEAFE;border-radius:12px;padding:16px;margin-bottom:20px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1D4ED8" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <p style="font-size:13px;color:#1E40AF;margin:0;">
                            تنها <strong>@fa($expStatus['days_remaining'])</strong> روز تا انقضای اشتراک باقی مانده!
                        </p>
                    </div>
                </div>
                @endif

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:24px;">
                    <div style="background:#F9FAFB;border-radius:12px;padding:16px;text-align:center;">
                        <div style="font-size:28px;font-weight:800;color:#111A6B;">
                            @fa($expStatus['days_remaining'])
                        </div>
                        <div style="font-size:12px;color:#9CA3AF;">روز باقی‌مانده</div>
                    </div>

                    <div style="background:#F9FAFB;border-radius:12px;padding:16px;text-align:center;">
                        <div style="font-size:16px;font-weight:700;color:#111A6B;">
                            {{ $expStatus['expires_at'] ?: '—' }}
                        </div>
                        <div style="font-size:12px;color:#9CA3AF;">تاریخ انقضا</div>
                    </div>

                    <div style="background:#F9FAFB;border-radius:12px;padding:16px;text-align:center;">
                        <div style="font-size:16px;font-weight:700;color:#111A6B;">
                            {{ $expStatus['plan_name'] ?: '—' }}
                        </div>
                        <div style="font-size:12px;color:#9CA3AF;">پلن فعلی</div>
                    </div>

                    <div style="background:#F9FAFB;border-radius:12px;padding:16px;text-align:center;">
                        <div style="font-size:14px;font-weight:500;color:#6B7280;" dir="ltr">
                            {{ $expStatus['license_key'] ?: '—' }}
                        </div>
                        <div style="font-size:12px;color:#9CA3AF;">کلید لایسنس</div>
                    </div>
                </div>

                @endif

                <div style="text-align:center;">
                    <button class="btn-primary" wire:click="$set('activeTab', 'plans')">
                        تمدید اشتراک
                    </button>
                </div>
            </section>

            <section class="card" style="padding:24px;margin-bottom:20px;">
                <h3 style="font-size:16px;font-weight:700;color:#111A6B;margin:0 0 20px;">
                    پلن‌های اشتراک
                </h3>

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;">
                    @forelse($plans ?? [] as $plan)
                    <div class="card"
                         style="padding:20px;border:2px solid {{ ($expStatus['plan_name'] ?? '') === $plan['name'] ? '#2E5BFF' : '#E5E7EB' }};
                                position:relative;overflow:hidden;">
                        @if(($expStatus['plan_name'] ?? '') === $plan['name'])
                        <div style="position:absolute;top:0;left:0;background:#2E5BFF;color:#fff;
                                    font-size:10px;padding:4px 12px;border-bottom-right-radius:8px;">
                            پلن فعلی
                        </div>
                        @endif

                        <h4 style="font-size:18px;font-weight:700;color:#111A6B;margin:12px 0 6px;">
                            {{ $plan['name'] }}
                        </h4>

                        <p style="font-size:13px;color:#6B7280;margin:0 0 16px;">
                            {{ $plan['description'] ?: '' }}
                        </p>

                        <div style="margin-bottom:16px;">
                            <div style="font-size:12px;color:#9CA3AF;">شهریه ماهانه</div>
                                <div style="font-size:22px;font-weight:800;color:#111A6B;">
                                    @faCurrency($plan['price_monthly'])
                                </div>
                        </div>

                        <div style="margin-bottom:20px;opacity:0.7;">
                            <div style="font-size:12px;color:#6B7280;">سالانه</div>
                            <div style="font-size:14px;font-weight:600;color:#111A6B;">
                                @faCurrency( $plan['price_yearly'] )
                            </div>
                        </div>

                        @if($plan['features'])
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:20px;">
                            @foreach($plan['features'] as $key => $value)
                                @if($value)
                                <span style="background:#EEF4FF;color:#2E5BFF;font-size:11px;
                                             padding:4px 10px;border-radius:20px;">
                                    {{ $key === 'sms_limit' ? "پیامک: {$value}" : $key }}
                                </span>
                                @endif
                            @endforeach
                        </div>
                        @endif

                        @if(($expStatus['plan_name'] ?? '') !== $plan['name'])
                        <button class="btn-primary" style="width:100%;justify-content:center;"
                                wire:click="selectPlan('{{ $plan['id'] }}')">
                            انتخاب پلن
                        </button>
                        @endif
                    </div>
                    @empty
                    <div style="grid-column:1/-1;text-align:center;padding:40px;color:#9CA3AF;">
                        پلنی یافت نشد.
                    </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    @if($showRenewalModal)
    <div class="modal-overlay open" style="z-index:250;">
        <div class="modal" style="max-width:380px;">
            <div style="text-align:center;margin-bottom:20px;">
                <div style="width:56px;height:56px;background:#EEF4FF;border-radius:50%;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2E5BFF" stroke-width="2">
                        <path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8"/>
                    </svg>
                </div>
                <h3 style="font-size:17px;font-weight:700;color:#111A6B;margin:0 0 8px;">
                    تمدید اشتراک
                </h3>
                <p style="font-size:13px;color:#6B7280;margin:0;">
                    پلن و دوره مورد نظر را انتخاب کنید
                </p>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:600;color:#6B7280;margin-bottom:6px;">
                    دوره پرداخت
                </label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    @php
                        $monthlyPrice = collect($plans)->firstWhere('id', $selectedPlanId)['price_monthly'] ?? 0;
                        $yearlyPrice = collect($plans)->firstWhere('id', $selectedPlanId)['price_yearly'] ?? 0;
                    @endphp
                    <button type="button"
                            wire:click="$set('selectedPeriod', 'monthly')"
                            class="{{ $selectedPeriod === 'monthly' ? 'btn-primary' : 'btn-ghost' }}"
                            style="flex-direction:column;align-items:center;">
                        <span style="font-size:13px;">ماهانه</span>
                        <span style="font-size:12px;opacity:0.8;">@faCurrency( $monthlyPrice )</span>
                    </button>
                    <button type="button"
                            wire:click="$set('selectedPeriod', 'yearly')"
                            class="{{ $selectedPeriod === 'yearly' ? 'btn-primary' : 'btn-ghost' }}"
                            style="flex-direction:column;align-items:center;">
                        <span style="font-size:13px;">سالانه</span>
                        <span style="font-size:12px;opacity:0.8;">@faCurrency( $yearlyPrice )</span>
                    </button>
                </div>
            </div>

            <div style="display:flex;gap:10px;justify-content:space-between;">
                <button class="btn-ghost" wire:click="$set('showRenewalModal', false)">
                    انصراف
                </button>
                <button class="btn-primary" wire:click="renewSubscription">
                    درخواست تمدید
                </button>
            </div>
        </div>
    </div>
    @endif

    <div wire:loading.delay wire:target="selectPlan,renewSubscription"
         style="position:fixed;bottom:24px;right:24px;background:#111A6B;color:#fff;
                padding:12px 20px;border-radius:10px;font-size:13px;z-index:500;">
        در حال پردازش...
    </div>
</div>
