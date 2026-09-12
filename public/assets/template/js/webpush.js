/**
 * WebPush Manager for SM-Attendance PWA
 */
const WebPushManager = {
    vapidPublicKey: null,
    isSubscribed: false,
    swRegistration: null,

    // Convert Base64URL to Uint8Array for VAPID applicationServerKey
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    },

    // Check support
    isSupported() {
        return ('serviceWorker' in navigator) && ('PushManager' in window) && ('Notification' in window);
    },

    // Initialize WebPush
    async init() {
        if (!this.isSupported()) {
            console.warn('WebPush: Push notifications not supported on this browser.');
            return;
        }

        try {
            this.swRegistration = await navigator.serviceWorker.ready;
            
            // Check existing subscription
            const subscription = await this.swRegistration.pushManager.getSubscription();
            this.isSubscribed = !(subscription === null);

            if (this.isSubscribed) {
                console.log('WebPush: Device is already subscribed to push notifications.');
                // Re-sync subscription to server silently in background
                this.sendSubscriptionToServer(subscription);
                this.updateUI(true);
            } else {
                console.log('WebPush: Device is not subscribed.');
                this.updateUI(false);
                this.showPromptBannerIfNeeded();
            }
        } catch (error) {
            console.error('WebPush init error:', error);
        }
    },

    // Fetch VAPID key from backend
    async getVapidPublicKey() {
        if (this.vapidPublicKey) return this.vapidPublicKey;
        try {
            const response = await fetch('/webpush/vapid-public-key');
            const data = await response.json();
            this.vapidPublicKey = data.publicKey;
            return this.vapidPublicKey;
        } catch (error) {
            console.error('WebPush: Failed to fetch VAPID public key', error);
            return null;
        }
    },

    // Helper to show visual messages safely
    showFeedback(type, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Berhasil' : (type === 'warning' ? 'Perhatian' : 'Info'),
                text: message,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            console.log(`[WebPush ${type}]`, message);
        }
    },

    // Subscribe user to push notifications
    async subscribeUser() {
        const btn = document.querySelector('#pushNotificationBanner .btn-primary') || document.querySelector('.btn-toggle-push-notif');
        const originalText = btn ? btn.innerHTML : '';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';
        }

        if (!this.isSupported()) {
            this.showFeedback('warning', 'Browser atau perangkat ini tidak mendukung Web Push Notification.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
            this.dismissPromptBanner();
            return false;
        }

        try {
            // Ensure service worker is ready
            if (!this.swRegistration) {
                this.swRegistration = await navigator.serviceWorker.ready;
            }

            const vapidKey = await this.getVapidPublicKey();
            if (!vapidKey) {
                console.warn('WebPush: No VAPID public key returned from server.');
                this.showFeedback('warning', 'Kunci Web Push (VAPID) belum aktif di server. Silakan hubungi admin.');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
                this.dismissPromptBanner();
                return false;
            }

            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                console.warn('WebPush: Notification permission denied or dismissed by user.');
                this.showFeedback('warning', 'Izin notifikasi tidak diaktifkan. Anda dapat mengaktifkannya di pengaturan browser/situs.');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
                this.dismissPromptBanner();
                return false;
            }

            const convertedVapidKey = this.urlBase64ToUint8Array(vapidKey);
            const subscription = await this.swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: convertedVapidKey
            });

            console.log('WebPush: Subscribed successfully:', subscription);
            await this.sendSubscriptionToServer(subscription);
            this.isSubscribed = true;
            this.updateUI(true);

            this.showFeedback('success', 'Notifikasi push berhasil diaktifkan di perangkat Anda!');
            this.hidePromptBanner();
            return true;
        } catch (error) {
            console.error('WebPush: Failed to subscribe user:', error);
            this.showFeedback('error', 'Gagal mengaktifkan notifikasi: ' + (error.message || error));
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
            this.dismissPromptBanner();
            return false;
        }
    },

    // Send subscription payload to Laravel Backend
    async sendSubscriptionToServer(subscription) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        try {
            const response = await fetch('/webpush/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify(subscription)
            });
            return await response.json();
        } catch (error) {
            console.error('WebPush: Error sending subscription to server:', error);
        }
    },

    // Unsubscribe
    async unsubscribeUser() {
        try {
            const subscription = await this.swRegistration.pushManager.getSubscription();
            if (subscription) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                await fetch('/webpush/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify({ endpoint: subscription.endpoint })
                });

                await subscription.unsubscribe();
                this.isSubscribed = false;
                this.updateUI(false);
                if (typeof toastr !== 'undefined') {
                    toastr.info('Notifikasi push telah dimatikan.');
                }
            }
        } catch (error) {
            console.error('WebPush: Error unsubscribing:', error);
        }
    },

    // Update UI states
    updateUI(isSubscribed) {
        const toggleButtons = document.querySelectorAll('.btn-toggle-push-notif');
        toggleButtons.forEach(btn => {
            if (isSubscribed) {
                btn.classList.remove('btn-primary', 'btn-outline-primary');
                btn.classList.add('btn-success');
                btn.innerHTML = '<ion-icon name="notifications"></ion-icon> Notifikasi Aktif';
            } else {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
                btn.innerHTML = '<ion-icon name="notifications-outline"></ion-icon> Aktifkan Notifikasi';
            }
        });

        if (isSubscribed) {
            this.hidePromptBanner();
        }
    },

    // Show floating prompt banner if not yet subscribed and not dismissed in current session
    showPromptBannerIfNeeded() {
        if (Notification.permission === 'denied' || this.isSubscribed) {
            return;
        }

        const dismissed = sessionStorage.getItem('push_prompt_dismissed');
        if (dismissed) return;

        const banner = document.getElementById('pushNotificationBanner');
        if (banner) {
            banner.style.display = 'block';
        }
    },

    hidePromptBanner() {
        const banner = document.getElementById('pushNotificationBanner');
        if (banner) {
            banner.style.display = 'none';
        }
    },

    dismissPromptBanner() {
        sessionStorage.setItem('push_prompt_dismissed', 'true');
        this.hidePromptBanner();
    }
};

// Auto-initialize when DOM and SW are ready
document.addEventListener('DOMContentLoaded', () => {
    // Delay slightly to not block initial render
    setTimeout(() => {
        WebPushManager.init();
    }, 1000);
});
