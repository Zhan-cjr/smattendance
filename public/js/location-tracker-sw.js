/**
 * Service Worker untuk Location Tracking
 * Handles background location sync bahkan ketika app ditutup
 * 
 * Browser Support:
 * - Chrome 49+, Edge 18+, Opera 36+
 * - Safari: Limited support
 * - Firefox: Limited support
 */

const SW_VERSION = 'location-tracker-v1.0';
const CACHE_NAME = `location-tracking-${SW_VERSION}`;
const LOCATION_SYNC_TAG = 'location-sync-background';

// Install Service Worker
self.addEventListener('install', (event) => {
    console.log('📍 [SW] Installing Service Worker:', SW_VERSION);
    self.skipWaiting();
});

// Activate Service Worker
self.addEventListener('activate', (event) => {
    console.log('📍 [SW] Activating Service Worker');
    event.waitUntil(clients.claim());
});

// Handle Background Sync (periodic)
self.addEventListener('sync', (event) => {
    console.log('📍 [SW] Sync event:', event.tag);
    
    if (event.tag === LOCATION_SYNC_TAG) {
        event.waitUntil(syncLocationBackground());
    }
});

// Handle Periodic Background Sync
// Triggered every 15-30 minutes even if app is closed
if ('periodicSync' in self.registration) {
    self.addEventListener('periodicsync', (event) => {
        console.log('📍 [SW] Periodic sync event:', event.tag);
        
        if (event.tag === LOCATION_SYNC_TAG) {
            event.waitUntil(syncLocationBackground());
        }
    });
}

/**
 * Sync location to server (background)
 * This runs even when app/tab is closed!
 */
async function syncLocationBackground() {
    try {
        console.log('🔄 [SW] Starting background location sync...');
        
        // Get stored user data from IndexedDB
        const userData = await getUserData();
        if (!userData || !userData.nik) {
            console.warn('⚠️ [SW] No user data found in IndexedDB');
            return;
        }

        // Get current location
        const position = await getCurrentPosition();
        if (!position) {
            console.warn('⚠️ [SW] Could not get location');
            return;
        }

        const { latitude, longitude, accuracy } = position.coords;

        // Send to server
        const response = await fetch('/monitoring-lokasi/saveLocation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': userData.csrfToken || ''
            },
            body: JSON.stringify({
                latitude: latitude,
                longitude: longitude,
                accuracy: accuracy
            })
        });

        if (response.ok) {
            const data = await response.json();
            console.log('✓ [SW] Location synced successfully:', {
                lat: latitude.toFixed(6),
                lng: longitude.toFixed(6),
                accuracy: accuracy.toFixed(2)
            });
            
            // Store last sync time
            await storeLastSyncTime();
        } else {
            console.warn('⚠️ [SW] Server returned status:', response.status);
        }
    } catch (error) {
        console.error('❌ [SW] Error syncing location:', error.message);
    }
}

/**
 * Get current position from device GPS
 */
function getCurrentPosition() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('Geolocation not supported'));
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                resolve(position);
            },
            (error) => {
                reject(error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
}

/**
 * Get user data from IndexedDB
 */
async function getUserData() {
    return new Promise((resolve) => {
        const request = indexedDB.open('LocationTrackerDB', 1);
        
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction('userData', 'readonly');
            const store = transaction.objectStore('userData');
            const getRequest = store.get('currentUser');
            
            getRequest.onsuccess = () => {
                resolve(getRequest.result || null);
            };
        };
        
        request.onerror = () => {
            resolve(null);
        };
    });
}

/**
 * Store last sync time
 */
async function storeLastSyncTime() {
    return new Promise((resolve) => {
        const request = indexedDB.open('LocationTrackerDB', 1);
        
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction('syncData', 'readwrite');
            const store = transaction.objectStore('syncData');
            store.put({
                id: 'lastSync',
                timestamp: new Date().toISOString()
            });
        };
        
        request.onerror = () => {
            console.warn('⚠️ [SW] Could not store sync time');
        };
    });
}

// Message handler dari clients
self.addEventListener('message', (event) => {
    const { type, data } = event.data;

    if (type === 'STORE_USER_DATA') {
        storeUserDataInIndexedDB(data);
    } else if (type === 'REGISTER_PERIODIC_SYNC') {
        registerPeriodicSync();
    }
});

/**
 * Store user data untuk background sync
 */
function storeUserDataInIndexedDB(userData) {
    const request = indexedDB.open('LocationTrackerDB', 1);
    
    request.onupgradeneeded = () => {
        const db = request.result;
        if (!db.objectStoreNames.contains('userData')) {
            db.createObjectStore('userData');
        }
        if (!db.objectStoreNames.contains('syncData')) {
            db.createObjectStore('syncData');
        }
    };
    
    request.onsuccess = () => {
        const db = request.result;
        const transaction = db.transaction('userData', 'readwrite');
        const store = transaction.objectStore('userData');
        store.put(userData, 'currentUser');
        console.log('✓ [SW] User data stored for background sync');
    };
}

/**
 * Register Periodic Background Sync
 * Triggers every 15-60 minutes depending on device/browser
 */
async function registerPeriodicSync() {
    if (!('periodicSync' in self.registration)) {
        console.warn('⚠️ [SW] Periodic Sync not supported');
        return;
    }

    try {
        await self.registration.periodicSync.register(LOCATION_SYNC_TAG, {
            minInterval: 15 * 60 * 1000 // Minimum 15 minutes
        });
        console.log('✓ [SW] Periodic background sync registered (15+ min interval)');
    } catch (error) {
        console.error('❌ [SW] Failed to register periodic sync:', error.message);
    }
}

// Fetch event untuk caching (optional)
self.addEventListener('fetch', (event) => {
    // Network first strategy untuk API calls
    if (event.request.url.includes('/api/') || event.request.url.includes('/monitoring-lokasi/')) {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    // If offline, return empty response
                    return new Response(JSON.stringify({ status: false }), {
                        status: 503,
                        statusText: 'Service Unavailable'
                    });
                })
        );
    }
});

console.log('✓ Service Worker loaded: location-tracker-sw.js');
