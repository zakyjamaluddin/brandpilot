// public/sw.js - BrandPilot AI PWA & Web Push Service Worker
const CACHE_NAME = 'brandpilot-pwa-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});

// ==========================================
// LISTENER PUSH NOTIFICATION NATIVE
// ==========================================
self.addEventListener('push', function(event) {
    if (!event.data) return;

    const payload = event.data.json();
    const title = payload.title || 'BrandPilot AI ✨';
    const options = {
        body: payload.body || 'Ada pembaruan dari BrandPilot AI.',
        icon: payload.icon || 'https://img.icons8.com/fluency/192/compass--v1.png',
        badge: 'https://img.icons8.com/fluency/96/compass--v1.png',
        data: {
            url: payload.url || '/admin'
        },
        vibrate: [200, 100, 200]
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Saat notifikasi diklik oleh user -> Buka halaman aplikasi
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const targetUrl = event.notification.data ? event.notification.data.url : '/admin';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            for (let client of windowClients) {
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
