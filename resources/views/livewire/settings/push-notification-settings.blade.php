<div dir="rtl">
    <div class="card" style="padding:24px;">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
            <div style="width:44px;height:44px;background:#EEF4FF;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2E5BFF" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </div>
            <div>
                <h4 style="font-size:15px;font-weight:700;color:#111A6B;margin:0 0 4px;">اعلان‌های پوش</h4>
                <p style="font-size:12px;color:#6B7280;margin:0;">دریافت اعلان نوبت‌ها و یادآوری‌ها در مرورگر</p>
            </div>
        </div>

        <div id="push-status" style="display:flex;align-items:center;gap:10px;padding:12px;border-radius:10px;
             background:{{ $subscribed ? '#F0FDF4' : '#F9FAFB' }};border:1px solid {{ $subscribed ? '#BBF7D0' : '#E5E7EB' }};">
            <div style="width:10px;height:10px;border-radius:50%;background:{{ $subscribed ? '#15803D' : '#D1D5DB' }};flex-shrink:0;"></div>
            <span style="font-size:13px;color:{{ $subscribed ? '#15803D' : '#6B7280' }};">
                {{ $subscribed ? 'اعلان‌ها فعال هستند' : 'اعلان‌ها غیرفعال هستند' }}
            </span>
        </div>

        <div style="display:flex;gap:10px;margin-top:16px;">
            @if(!$subscribed)
            <button id="push-enable-btn" onclick="enablePush()" class="btn-primary"
                    style="font-size:13px;padding:8px 20px;">
                فعال‌سازی اعلان‌ها
            </button>
            @else
            <button onclick="disablePush()" class="btn-ghost"
                    style="font-size:13px;padding:8px 20px;color:#991B1B;">
                غیرفعال‌سازی
            </button>
            @endif
        </div>

        <p id="push-error" style="font-size:12px;color:#991B1B;margin-top:10px;display:none;"></p>
    </div>

    <script>
        async function enablePush() {
            const errorEl = document.getElementById('push-error');
            errorEl.style.display = 'none';

            if (!('PushManager' in window) || !('serviceWorker' in navigator)) {
                errorEl.textContent = 'مرورگر شما از اعلان‌های پوش پشتیبانی نمی‌کند.';
                errorEl.style.display = 'block';
                return;
            }

            try {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    errorEl.textContent = 'دسترسی اعلان رد شد. لطفاً از تنظیمات مرورگر فعال کنید.';
                    errorEl.style.display = 'block';
                    return;
                }

                const reg = await navigator.serviceWorker.ready;
                const keyResp = await fetch('/push/vapid-key');
                const { publicKey } = await keyResp.json();

                if (!publicKey) {
                    errorEl.textContent = 'کلید VAPID تنظیم نشده است. لطفاً با مدیر سیستم تماس بگیرید.';
                    errorEl.style.display = 'block';
                    return;
                }

                const subscription = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(publicKey),
                });

                const resp = await fetch('/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(subscription.toJSON()),
                });

                if (resp.ok) {
                    Livewire.dispatch('refresh-component');
                } else {
                    errorEl.textContent = 'خطا در ثبت اشتراک.';
                    errorEl.style.display = 'block';
                }
            } catch (e) {
                errorEl.textContent = 'خطا: ' + e.message;
                errorEl.style.display = 'block';
            }
        }

        async function disablePush() {
            const errorEl = document.getElementById('push-error');
            errorEl.style.display = 'none';

            try {
                const reg = await navigator.serviceWorker.ready;
                const subscription = await reg.pushManager.getSubscription();

                if (subscription) {
                    await subscription.unsubscribe();

                    await fetch('/push/unsubscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ endpoint: subscription.endpoint }),
                    });
                }

                Livewire.dispatch('refresh-component');
            } catch (e) {
                errorEl.textContent = 'خطا: ' + e.message;
                errorEl.style.display = 'block';
            }
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    </script>
</div>
