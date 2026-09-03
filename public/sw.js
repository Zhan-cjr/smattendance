

// Service Worker untuk SM-Attendance GPS V2
// TIDAK akan cache file apapun - semua data selalu fresh dari network
// FIX: Tidak mengintervensi navigation request (HTML) untuk mencegah blank page

// Install event - tidak cache apapun
self.addEventListener('install', event => {
    // Skip waiting untuk update service worker lebih cepat
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
                return Promise.resolve();
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
        event.ports[0].postMessage({ version: '1.0.0-on-sw' });
    }
});

console.log('Service Worker: Unified Mode initialized');

// Message handler untuk komunikasi dengan main thread
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }

    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: '1.0.0-no-cache' });
    }
});

console.log('Service Worker: No Cache Mode initialized');
