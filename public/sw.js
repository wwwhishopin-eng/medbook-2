const CACHE_NAME = 'medboard-v1';
const OFFLINE_URL = '/offline.html';

const PRECACHE_URLS = [
    '/',
    '/offline.html',
    '/manifest.json',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((names) =>
            Promise.all(names.filter((n) => n !== CACHE_NAME).map((n) => caches.delete(n)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() =>
                caches.match(OFFLINE_URL)
            )
        );
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => {
            if (cached) return cached;
            return fetch(event.request).then((response) => {
                if (!response || response.status !== 200 || response.type !== 'basic') return response;
                const clone = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                return response;
            }).catch(() => {
                if (event.request.destination === 'image') {
                    return new Response('<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><text y="50" x="50" text-anchor="middle" fill="#ccc" font-size="14">آفلاین</text></svg>', { headers: { 'Content-Type': 'image/svg+xml' } });
                }
                return new Response('', { status: 503, statusText: 'Offline' });
            });
        })
    );
});

self.addEventListener('push', (event) => {
    let data = { title: 'MedBoard', body: 'اعلان جدید', icon: '/icons/icon-192x192.png' };
    if (event.data) {
        try { data = event.data.json(); } catch (e) { data.body = event.data.text(); }
    }
    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon || '/icons/icon-192x192.png',
            badge: '/icons/icon-96x96.png',
            dir: 'rtl',
            lang: 'fa',
            vibrate: [100, 50, 100],
            data: data.url ? { url: data.url } : {},
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/';
    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
            for (const client of clients) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(url);
                    return client.focus();
                }
            }
            return self.clients.openWindow(url);
        })
    );
});
