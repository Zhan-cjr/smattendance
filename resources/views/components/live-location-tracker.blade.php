{{-- Live Location Tracker Component --}}
{{-- Include this component in dashboard view untuk auto-tracking karyawan dengan spy=1 --}}
{{-- Automatically captures GPS location in foreground (real-time) --}}
{{-- + Background sync via Service Worker (15-60 minute intervals when app closed) --}}

@php
    $user = auth()->user();
    // Get employee data through userkaryawan relation
    $userKaryawan = $user ? $user->userkaryawan : null;
    $employee = $userKaryawan ? $userKaryawan->karyawan : null;
    $isSpy = $employee && $employee->spy == 1;
    
    // Get general settings for monitoring configuration
    $generalSetting = App\Models\Pengaturanumum::first();
    $enableMonitoring = $generalSetting ? $generalSetting->enable_live_location_monitoring : true;
    $monitoringMode = $generalSetting ? $generalSetting->monitoring_live_location_mode : 0;
    
    // Check if employee has checked in today (for working hours mode)
    $hasCheckedInToday = false;
    if ($employee && $monitoringMode == 1) {
        $today = now()->toDateString();
        $presensi = App\Models\Presensi::where('nik', $employee->nik)
            ->where('tanggal', $today)
            ->whereNotNull('jam_in')
            ->whereNull('jam_out')
            ->first();
        $hasCheckedInToday = $presensi ? true : false;
    }
@endphp

@if($isSpy)
<div id="liveLocationTracker" style="display: none;">
    {{-- Component ini berjalan di background --}}
</div>

<script>
    // Live Location Tracker Configuration
    const LocationTracker = {
        enabled: {{ $isSpy ? 'true' : 'false' }},
        enableMonitoring: {{ $enableMonitoring ? 'true' : 'false' }},
        monitoringMode: {{ $monitoringMode }},
        hasCheckedInToday: {{ $hasCheckedInToday ? 'true' : 'false' }},
        nik: '{{ $employee->nik ?? "" }}',
        trackingInterval: 30000, // 30 seconds - optimal balance between accuracy and battery
        watchId: null,
        lastLatitude: null,
        lastLongitude: null,
        minAccuracy: 50, // meters - lowered from 100 to account for consumer GPS accuracy (typically 20-50m)
        maxAccuracy: 300, // Reduced from 500 to 300 for better location quality
        failedAttempts: 0,
        maxFailedAttempts: 5,
        isInitialized: false,
        hasServiceWorker: false,

        /**
         * Initialize live location tracking
         */
        init() {
            if (!this.enabled) {
                console.log('📍 Live Location Tracker: Disabled (spy=0)');
                return;
            }

            if (!this.enableMonitoring) {
                console.log('📍 Live Location Tracker: Disabled by admin settings');
                return;
            }

            if (this.monitoringMode === 1 && !this.hasCheckedInToday) {
                console.log('📍 Live Location Tracker: Working hours mode - no check-in today, skipping tracking');
                return;
            }

            console.log('📍 Live Location Tracker initialized for NIK: ' + this.nik);
            console.log('ℹ️ Mode:', this.monitoringMode === 0 ? '24 Hours' : 'Working Hours');
            console.log('ℹ️ Foreground: Real-time tracking every 30 seconds');
            console.log('ℹ️ Background: Periodic sync every 15-60 minutes (if Service Worker active)');

            // Check if browser supports geolocation
            if (!navigator.geolocation) {
                console.warn('⚠️ Geolocation not supported by this browser');
                return;
            }

            this.isInitialized = true;

            // Check for Service Worker support
            this.checkServiceWorkerStatus();

            // Don't wait for permission - start tracking immediately
            // Permission will be requested in background
            this.startTracking();
            this.requestLocationPermission();
        },

        /**
         * Check Service Worker status and background sync capability
         */
        checkServiceWorkerStatus() {
            if (!('serviceWorker' in navigator)) {
                console.warn('⚠️ Service Worker not supported - background tracking unavailable');
                return;
            }

            navigator.serviceWorker.ready.then(registration => {
                this.hasServiceWorker = true;
                console.log('✓ Service Worker active - background tracking enabled');
                
                // Check periodic sync support
                if ('periodicSync' in registration) {
                    console.log('✓ Periodic Background Sync supported (15-60 min intervals)');
                    console.log('ℹ️ Location will continue syncing even when app is closed');
                } else {
                    console.warn('⚠️ Periodic Background Sync not supported - only foreground tracking');
                }
            }).catch(error => {
                console.warn('⚠️ Service Worker not ready:', error.message);
            });
        },

        /**
         * Request geolocation permission from user (non-blocking)
         */
        requestLocationPermission() {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    console.log('✓ Location permission granted');
                    // Permission granted, good to continue tracking
                },
                (error) => {
                    console.warn('⚠️ Location permission status:', error.code === 1 ? 'PERMISSION_DENIED' : error.message);
                    if (error.code === 1) {
                        this.showPermissionNotice();
                    }
                },
                { 
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        },

        /**
         * Start continuous location tracking
         */
        startTracking() {
            console.log('🟢 Location tracking started - updating every ' + (this.trackingInterval/1000) + ' seconds');

            // Watch position for continuous tracking
            this.watchId = navigator.geolocation.watchPosition(
                (position) => this.handleLocationUpdate(position),
                (error) => this.handleLocationError(error),
                {
                    enableHighAccuracy: true,
                    timeout: 20000,
                    maximumAge: 5000
                }
            );

            // Also do immediate tracking and periodic backup tracking
            this.trackLocationNow();
            this.backupTrackingInterval = setInterval(() => this.trackLocationNow(), this.trackingInterval);
        },

        /**
         * Handle location updates from watchPosition
         */
        handleLocationUpdate(position) {
            const { latitude, longitude, accuracy } = position.coords;
            
            // Check if accuracy is acceptable (within reasonable GPS range)
            if (accuracy > this.maxAccuracy) {
                console.log(`⚠️ Accuracy ${accuracy.toFixed(2)}m exceeds maximum ${this.maxAccuracy}m, skipping`);
                return;
            }

            // Log accuracy status
            if (accuracy > this.minAccuracy) {
                console.log(`⚠️ Accuracy ${accuracy.toFixed(2)}m (threshold: ${this.minAccuracy}m) - still sending due to lack of better data`);
            }

            // Check if location changed significantly (more than 5 meters for sensitivity)
            if (this.lastLatitude && this.lastLongitude) {
                const distance = this.getDistance(
                    this.lastLatitude, this.lastLongitude,
                    latitude, longitude
                );
                if (distance < 5) {
                    return; // Don't send if moved less than 5 meters
                }
            }

            this.sendLocationToServer(latitude, longitude, accuracy);
        },

        /**
         * Handle location errors
         */
        handleLocationError(error) {
            console.error('❌ Location tracking error:', {
                code: error.code,
                message: error.message,
                PERMISSION_DENIED: 1,
                POSITION_UNAVAILABLE: 2,
                TIMEOUT: 3
            });
        },

        /**
         * Track location now (immediate) - with better error recovery
         */
        trackLocationNow() {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude, accuracy } = position.coords;
                    
                    // Check if accuracy is within acceptable range
                    if (accuracy > this.maxAccuracy) {
                        console.warn(`⚠️ Location accuracy ${accuracy.toFixed(2)}m exceeds max ${this.maxAccuracy}m`);
                        return;
                    }

                    // Send even if accuracy is not perfect (>minAccuracy) but within maxAccuracy
                    // This ensures we get data even in less ideal conditions
                    if (accuracy <= this.maxAccuracy) {
                        this.sendLocationToServer(latitude, longitude, accuracy);
                        this.failedAttempts = 0; // Reset failed attempts on success
                    }
                },
                (error) => {
                    this.failedAttempts++;
                    console.warn(`❌ Failed to get position (attempt ${this.failedAttempts}/${this.maxFailedAttempts}):`, error.message);
                    
                    // Stop tracking after too many failed attempts
                    if (this.failedAttempts >= this.maxFailedAttempts) {
                        console.error('❌ Location tracking failed after multiple attempts. Please check GPS and permissions.');
                        this.stop();
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 20000,
                    maximumAge: 2000 // Allow 2 seconds old data if GPS is slow
                }
            );
        },

        /**
         * Send location to server
         */
        sendLocationToServer(latitude, longitude, accuracy) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!csrfToken) {
                console.error('❌ CSRF token not found in meta tags');
                return;
            }

            const endpoint = '{{ route("monitoring-lokasi.saveLocation") }}';
            const payload = {
                latitude: parseFloat(latitude.toFixed(6)),
                longitude: parseFloat(longitude.toFixed(6)),
                accuracy: parseFloat(accuracy.toFixed(2))
            };

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status) {
                    console.log(`✓ Location tracked: ${latitude.toFixed(6)}, ${longitude.toFixed(6)} (accuracy: ${accuracy.toFixed(2)}m)`);
                    this.lastLatitude = latitude;
                    this.lastLongitude = longitude;
                    this.failedAttempts = 0;
                } else {
                    console.warn('⚠️ Server returned status false:', data.message);
                }
            })
            .catch(error => {
                this.failedAttempts++;
                console.error('❌ Failed to send location:', error.message);
            });
        },

        /**
         * Calculate distance between two coordinates (Haversine formula)
         */
        getDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Earth's radius in meters
            const φ1 = (lat1 * Math.PI) / 180;
            const φ2 = (lat2 * Math.PI) / 180;
            const Δφ = ((lat2 - lat1) * Math.PI) / 180;
            const Δλ = ((lon2 - lon1) * Math.PI) / 180;

            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c;
        },

        /**
         * Check if tracking should be active based on current settings and presensi status
         */
        shouldTrack() {
            if (!this.enabled || !this.enableMonitoring) {
                return false;
            }

            if (this.monitoringMode === 0) {
                // 24 hours mode - always track if enabled
                return true;
            } else if (this.monitoringMode === 1) {
                // Working hours mode - check presensi status
                return this.checkPresensiStatus();
            }

            return false;
        },

        /**
         * Check current presensi status via AJAX
         */
        checkPresensiStatus() {
            return fetch('{{ route("presensi.checkStatus") }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const hasCheckedIn = data.hasCheckedIn || false;
                console.log('📊 Presensi status check:', hasCheckedIn ? 'Checked In' : 'Not Checked In');
                return hasCheckedIn;
            })
            .catch(error => {
                console.error('❌ Failed to check presensi status:', error);
                return false;
            });
        },

        /**
         * Start/stop tracking based on current status
         */
        async updateTrackingStatus() {
            const shouldTrackNow = await this.shouldTrack();
            
            if (shouldTrackNow && !this.isInitialized) {
                console.log('▶️ Starting location tracking (status changed)');
                this.init();
            } else if (!shouldTrackNow && this.isInitialized) {
                console.log('⏸️ Stopping location tracking (status changed)');
                this.stop();
                this.isInitialized = false;
            }
        },

        /**
         * Show notice if permission denied
         */
        showPermissionNotice() {
            const notice = document.createElement('div');
            notice.className = 'alert alert-warning alert-dismissible fade show m-3';
            notice.innerHTML = `
                <i class="tf-icons ti ti-location-off"></i>
                <strong>Location Permission Denied</strong>
                <p class="mb-0">Untuk mengaktifkan tracking lokasi real-time, silakan izinkan akses GPS di browser Anda.</p>
                <small class="text-muted">Klik lock icon di address bar → Permission Settings → Allow Location</small>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.insertBefore(notice, document.body.firstChild);
        },

        /**
         * Stop tracking
         */
        stop() {
            if (this.watchId) {
                navigator.geolocation.clearWatch(this.watchId);
                console.log('⛔ Location tracking stopped');
            }
            if (this.backupTrackingInterval) {
                clearInterval(this.backupTrackingInterval);
            }
        }
    };

    // Initialize when document is ready
    document.addEventListener('DOMContentLoaded', () => {
        LocationTracker.init();
        
        // For working hours mode, check presensi status every 30 seconds
        if (LocationTracker.monitoringMode === 1) {
            setInterval(async () => {
                await LocationTracker.updateTrackingStatus();
            }, 30000); // Check every 30 seconds
        }
    });

    // Stop tracking when page unloads
    window.addEventListener('beforeunload', () => {
        LocationTracker.stop();
    });

    // Export for manual control
    window.LocationTracker = LocationTracker;
</script>
@endif
