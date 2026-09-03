{{-- PWA Service Worker Registration Component --}}
{{-- This component registers the service worker and enables periodic background sync --}}

@php
    $user = auth()->user();
    $userKaryawan = $user ? $user->userkaryawan : null;
    $employee = $userKaryawan ? $userKaryawan->karyawan : null;
    $isSpy = $employee && $employee->spy == 1;
@endphp

@if($isSpy)
<script>
    /**
     * Register Service Worker for background location tracking
     * Allows tracking even when app/browser is closed
     */
    async function registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            console.warn('⚠️ Service Worker not supported in this browser');
            return;
        }

        try {
            // Register Service Worker
            const registration = await navigator.serviceWorker.register(
                '{{ asset("js/location-tracker-sw.js") }}',
                { scope: '/js/' }
            );

            console.log('✓ Service Worker registered successfully');

            // Store user data for background sync
            storeUserDataForSync(registration);

            // Request Periodic Background Sync permission
            if ('permissions' in navigator) {
                navigator.permissions.query({ name: 'periodic-background-sync' })
                    .then(result => {
                        if (result.state === 'granted') {
                            registerPeriodicSync(registration);
                        } else if (result.state === 'prompt') {
                            console.log('ℹ️ Periodic sync permission needs to be granted');
                        }
                    })
                    .catch(error => {
                        console.warn('⚠️ Could not query periodic sync permission:', error);
                    });
            } else {
                // Fallback: try to register without checking permission
                registerPeriodicSync(registration);
            }

        } catch (error) {
            console.error('❌ Service Worker registration failed:', error);
        }
    }

    /**
     * Store user data in IndexedDB for Service Worker to use
     */
    function storeUserDataForSync(registration) {
        const userData = {
            nik: '{{ $employee->nik ?? "" }}',
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
            userId: {{ auth()->id() }},
            storedAt: new Date().toISOString()
        };

        // Send message to Service Worker to store data
        if (navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({
                type: 'STORE_USER_DATA',
                data: userData
            });
        } else {
            // If no controller yet, wait for it
            navigator.serviceWorker.ready.then(registration => {
                registration.active.postMessage({
                    type: 'STORE_USER_DATA',
                    data: userData
                });
            });
        }

        console.log('✓ User data sent to Service Worker for background sync');
    }

    /**
     * Register Periodic Background Sync
     * Will trigger every 15-60 minutes depending on device
     */
    async function registerPeriodicSync(registration) {
        if (!('periodicSync' in registration)) {
            console.warn('⚠️ Periodic Background Sync not supported');
            console.log('ℹ️ Fallback: Location tracking will work when app is active');
            return;
        }

        try {
            await registration.periodicSync.register('location-sync-background', {
                minInterval: 15 * 60 * 1000 // 15 minutes minimum
            });

            console.log('✓ Periodic Background Sync registered');
            console.log('ℹ️ Location will sync every 15-60 minutes even when app is closed');

        } catch (error) {
            console.error('❌ Failed to register periodic sync:', error);

            // Check if it's a permission issue
            if (error.name === 'NotAllowedError') {
                console.warn('⚠️ Permission denied for periodic background sync');
                console.log('ℹ️ Enable in browser settings: Settings → Site Settings → Periodic background sync');
            }
        }
    }

    /**
     * Handle Service Worker updates
     */
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            console.log('✓ Service Worker updated/activated');
            storeUserDataForSync(navigator.serviceWorker.ready);
        });
    }

    // Register Service Worker when page loads
    document.addEventListener('DOMContentLoaded', () => {
        registerServiceWorker();
    });

    // Also try registering on page unload to ensure SW is ready
    // even if page navigation happens
    window.addEventListener('beforeunload', () => {
        if (navigator.serviceWorker.controller) {
            // Send final location sync message
            navigator.serviceWorker.controller.postMessage({
                type: 'SYNC_LOCATION_NOW'
            });
        }
    });
</script>
@endif
