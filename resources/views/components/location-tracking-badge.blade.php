{{-- Location Tracking Status Badge Component --}}

@php
    $user = auth()->user();
    $userKaryawan = $user ? $user->userkaryawan : null;
    $employee = $userKaryawan ? $userKaryawan->karyawan : null;
    $isSpy = $employee && $employee->spy == 1;
@endphp

@if($isSpy)
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
        <div class="flex-grow-1">
            <h6 class="alert-heading mb-1">
                <i class="tf-icons ti ti-location-filled" style="color: #0dcaf0;"></i>
                Live Location Tracking Aktif
            </h6>
            <small class="d-block mb-2">
                <strong>Foreground (Real-Time):</strong> Lokasi Anda di-track setiap 30 detik saat app aktif.
                <br/>
                <strong>Background (PWA):</strong> Lokasi akan terus sync setiap 15-60 menit meskipun app ditutup (jika PWA diinstall).
                <br/>
                <span style="font-size: 11px; color: #666;">ℹ️ Pastikan GPS aktif dan izinkan permission lokasi di browser.</span>
            </small>
            <div class="badge bg-info" id="trackingStatus">
                <i class="ti ti-point-filled"></i> Menunggu lokasi...
            </div>
            <div class="badge bg-primary" id="pwaStatus" style="margin-left: 5px; display: none;">
                <i class="ti ti-cloud-upload"></i> PWA Background Ready
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const statusBadge = document.getElementById('trackingStatus');
        const pwaStatus = document.getElementById('pwaStatus');
        
        // Check PWA/Service Worker status
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.ready.then(registration => {
                pwaStatus.style.display = 'inline-block';
                if ('periodicSync' in registration) {
                    pwaStatus.innerHTML = '<i class="ti ti-cloud-upload"></i> PWA Background Sync Active';
                }
            }).catch(() => {
                pwaStatus.style.display = 'none';
            });
        }
        
        // Update status setiap 5 detik
        setInterval(() => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const accuracy = position.coords.accuracy.toFixed(2);
                        const time = new Date().toLocaleTimeString();
                        statusBadge.innerHTML = `
                            <i class="ti ti-point-filled" style="color: #28a745;"></i>
                            Tracking Aktif - Accuracy: ${accuracy}m - Update: ${time}
                        `;
                    },
                    () => {
                        statusBadge.innerHTML = `
                            <i class="ti ti-point-filled" style="color: #ffc107;"></i>
                            Tracking Aktif - Menunggu GPS...
                        `;
                    }
                );
            }
        }, 5000);
    });
</script>
@endif
