// Service Worker untuk SM-Attendance GPS V2
// TIDAK akan cache file apapun - semua data selalu fresh dari network

// Install event
self.addEventListener('install', event => {
    self.skipWaiting();
});

// Activate event - clear semua cache yang ada
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        return caches.delete(cacheName);
                    })
                );
            })
            .then(() => {
                return self.clients.claim();
            })
    );
});

// ==========================================
// WEB PUSH NOTIFICATIONS EVENT LISTENER
// ==========================================
self.addEventListener('push', event => {
    if (!event.data) {
        console.log('WebPush: Push event without data received.');
        return;
    }

    let payload = {};
    try {
        payload = event.data.json();
    } catch (e) {
        payload = {
            title: 'SM-Attendance',
            body: event.data.text()
        };
    }

    const title = payload.title || 'SM-Attendance';
    const options = {
        body: payload.body || '',
        icon: payload.icon || '/logo.png',
        badge: payload.badge || '/logo.png',
        vibrate: payload.vibrate || [100, 50, 100],
        data: payload.data || { url: payload.url || '/' },
        tag: payload.tag || 'smatt-notification-' + Date.now(),
        renotify: true,
        requireInteraction: payload.requireInteraction || false,
        actions: payload.actions || []
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// ==========================================
// NOTIFICATION CLICK EVENT LISTENER
// ==========================================
self.addEventListener('notificationclick', event => {
    event.notification.close();

    const targetUrl = (event.notification.data && event.notification.data.url) 
        ? event.notification.data.url 
        : '/';

    // Handle action buttons if clicked
    if (event.action && event.action === 'dismiss') {
        return;
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            // Check if there is already a window open with target origin
            for (let i = 0; i < windowClients.length; i++) {
                const client = windowClients[i];
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            // If no window is open, open a new one
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// Background sync untuk presensi offline (opsional)
self.addEventListener('sync', event => {
    if (event.tag === 'background-sync-presensi') {
        event.waitUntil(doBackgroundSync());
    }
});

async function doBackgroundSync() {
    // Implementasi sync data presensi jika diperlukan
}

// Message handler untuk komunikasi dengan main thread
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }

    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: '2.0.0-webpush' });
    }
});

console.log('Service Worker: SM-Attendance WebPush V2 initialized');
