@extends('layouts.mobile.app')
@section('content')
    <style>
        /* Modern Clean Mobile Layout */
        :root {
            --primary-color: #0f172a;
            --accent-color: #3b82f6;
            --success-color: #22c55e;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
        }

        body {
            background-color: #000;
            overflow: hidden; /* Prevent scrolling on capture screen */
        }

        #header-section {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 15px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
        }

        .webcam-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            background: #000;
        }

        .webcam-capture, .webcam-capture video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
        }

        /* Information Overlays */
        .info-overlay {
            position: absolute;
            top: 80px;
            left: 20px;
            right: 20px;
            z-index: 10;
        }

        .time-card {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 15px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .current-time {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            margin-bottom: 5px;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .current-date {
            font-size: 0.9rem;
            text-align: center;
            opacity: 0.9;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .schedule-info {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 10px;
            font-size: 0.85rem;
        }

        .schedule-item {
            text-align: center;
            flex: 1;
        }

        .schedule-label {
            font-size: 0.7rem;
            opacity: 0.8;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .schedule-time {
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Map Preview Mini */
        .map-wrapper {
            position: absolute;
            bottom: 180px;
            right: 20px;
            width: 80px;
            height: 80px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.5);
            z-index: 10;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            background: rgba(0,0,0,0.5);
        }
        
        #map {
            width: 100%;
            height: 100%;
        }

        .location-status {
            position: absolute;
            bottom: 180px;
            left: 20px;
            z-index: 10;
            background: rgba(0,0,0,0.6);
            padding: 6px 12px;
            border-radius: 20px;
            color: #fff;
            font-size: 0.8rem;
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: var(--danger-color); /* Default red until locked */
            border-radius: 50%;
            display: inline-block;
        }

        .status-dot.active {
            background-color: var(--success-color);
            box-shadow: 0 0 8px var(--success-color);
        }

        /* Action Area */
        .action-area {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            padding-bottom: 90px; /* Safe area + Bottom Nav */
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0) 100%);
            z-index: 20;
            display: flex;
            justify-content: center;
        }

        .capture-btn {
            width: 100%;
            height: 56px;
            border-radius: 28px;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: transform 0.2s active;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .capture-btn:active {
            transform: scale(0.98);
        }

        .btn-start {
            background: linear-gradient(135deg, var(--accent-color), #2563eb);
        }

        .btn-end {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
        }

        .btn-loading {
            opacity: 0.8;
            pointer-events: none;
        }
        
        .back-btn {
            color: #fff;
            font-size: 1.5rem;
            background: rgba(0,0,0,0.3);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
            text-decoration: none;
        }

    </style>

    <!-- Header with Back Button -->
    <div id="header-section">
        <a href="javascript:history.back()" class="back-btn">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </a>
    </div>

    <!-- Main Webcam Container -->
    <div class="webcam-container">
        <div class="webcam-capture"></div>
    </div>

    <!-- Info Overlay -->
    <div class="info-overlay">
        <div class="time-card">
            <div class="current-time" id="jam">00:00:00</div>
            <div class="current-date">{{ DateToIndo(date('Y-m-d')) }}</div>
            
            <div class="schedule-info">
                <div class="schedule-item">
                    <div class="schedule-label">Mulai Lembur</div>
                    <div class="schedule-time">{{ date('H:i', strtotime($lembur->lembur_mulai)) }}</div>
                </div>
                <div class="schedule-item">
                    <div class="schedule-label">Selesai Lembur</div>
                    <div class="schedule-time">{{ date('H:i', strtotime($lembur->lembur_selesai)) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Location Status & Map Preview -->
    <div class="location-status">
        <span class="status-dot" id="gps-status-dot"></span>
        <span id="gps-status-text">Mencari Lokasi...</span>
    </div>
    
    <div class="map-wrapper">
        <div id="map"></div>
    </div>

    <!-- Action Buttons -->
    <div class="action-area">
        @if ($lembur->lembur_in == null)
            <button class="capture-btn btn-start" id="btn-action">
                <ion-icon name="finger-print-outline" style="font-size: 24px;"></ion-icon>
                <span>Mulai Lembur</span>
            </button>
            <input type="hidden" id="status" value="1">
        @else
            <button class="capture-btn btn-end" id="btn-action">
                <ion-icon name="finger-print-outline" style="font-size: 24px;"></ion-icon>
                <span>Selesai Lembur</span>
            </button>
            <input type="hidden" id="status" value="2">
        @endif
        <input type="hidden" id="id_lembur" value="{{ $lembur->id }}">
    </div>

    <!-- Sound Effects -->
    <audio id="notifikasi_radus"><source src="{{ asset('assets/sound/radius.mp3') }}" type="audio/mpeg"></audio>
    <audio id="notifikasi_success"><source src="{{ asset('assets/sound/absenmasuk.wav') }}" type="audio/mpeg"></audio>
    <audio id="notifikasi_error"><source src="{{ asset('assets/sound/absenpulang.mp3') }}" type="audio/mpeg"></audio>

    <!-- Required Libs -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

@endsection

@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Clock Function ---
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const jamElement = document.getElementById('jam');
            if (jamElement) {
                jamElement.textContent = `${h}:${m}:${s}`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    });

    // --- Webcam & Location Logic ---
    $(function() {
        let lokasi_user = null;
        let map = null;
        let lokasi_cabang = "{{ $lembur->kode_cabang }}"; // Assuming passing code, or maybe location string itself?
        // Note: In old file, it tried to use $lembur->kode_cabang, but also $lembur->lokasi_cabang from controller query.
        // Let's check view data: createpresensi joins cabang table.
        // So $lembur->lokasi_cabang should be available.
        let lokasi_kantor_str = "{{ $lembur->lokasi_cabang }}";
        let radius_cabang = "{{ $lembur->radius_cabang }}";
        
        // Handle if split fails
        let lat_kantor = 0;
        let long_kantor = 0;
        if(lokasi_kantor_str.includes(',')){
             let loc = lokasi_kantor_str.split(',');
             lat_kantor = loc[0];
             long_kantor = loc[1];
        }
        
        // Detect Mobile
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        // Init Webcam
        if (document.querySelector('.webcam-capture')) {
            Webcam.set({
                width: isMobile ? 480 : 640,
                height: isMobile ? 360 : 480,
                image_format: 'jpeg',
                jpeg_quality: 80,
                constraints: {
                    facingMode: 'user'
                }
            });
            
            try {
                Webcam.attach('.webcam-capture');
            } catch(e) {
                console.error("Webcam Error:", e);
                Swal.fire({icon: 'error', title: 'Error Kamera', text: 'Gagal mengakses kamera.'});
            }
        }

        // Init Map & Geolocation
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(successCallback, errorCallback, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            });
        }

        function successCallback(position) {
            lokasi_user = position.coords.latitude + "," + position.coords.longitude;
            
            // Update UI
            const statusDot = document.getElementById('gps-status-dot');
            const statusText = document.getElementById('gps-status-text');
            
            if (statusDot) statusDot.classList.add('active');
            if (statusText) statusText.textContent = "Lokasi Terkunci";
            
            // Init Mini Map if not exists
            if (!map && document.getElementById('map')) {
                map = L.map('map', {
                    zoomControl: false,
                    attributionControl: false
                }).setView([position.coords.latitude, position.coords.longitude], 16);
                
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                
                // User Marker
                L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
                
                // Office Circle
                if(lat_kantor != 0){
                    L.circle([lat_kantor, long_kantor], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.2,
                        radius: radius_cabang
                    }).addTo(map);
                }
            } else if (map) {
                map.setView([position.coords.latitude, position.coords.longitude]);
            }
        }

        function errorCallback(error) {
            console.error("Geolocation Error:", error);
            const statusText = document.getElementById('gps-status-text');
            if(statusText) statusText.textContent = "Gagal Lokasi!";
        }

        // --- Action Button Logic ---
        $('#btn-action').click(function(e) {
            e.preventDefault();
            
            if (!lokasi_user) {
                Swal.fire({icon: 'warning', title: 'Oops!', text: 'Lokasi Anda belum terdeteksi. Tunggu sejenak.'});
                return;
            }

            const btn = $(this);
            const status = $('#status').val();
            const id_lembur = $('#id_lembur').val();
            const originalText = btn.html();

            // Set loading state
            btn.prop('disabled', true).addClass('btn-loading').html('<div class="spinner-border spinner-border-sm text-light" role="status"></div> Loading...');

            // Capture Image
            Webcam.snap(function(data_uri) {
                
                // Convert to Blob
                var blob = dataURItoBlob(data_uri);
                var formData = new FormData();
                formData.append('image', blob, 'image.jpg');
                formData.append('lokasi', lokasi_user);
                formData.append('status', status);
                formData.append('id_lembur', id_lembur);
                formData.append('_token', "{{ csrf_token() }}");
                
                $.ajax({
                    type: 'POST',
                    url: '/lembur/storepresensi',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(respond) {
                        if (respond.status == true) {
                            // document.getElementById('notifikasi_success').play();
                            Swal.fire({
                                title: 'Berhasil!',
                                text: respond.message,
                                icon: 'success'
                            }).then(() => {
                                window.location.href = '/lembur';
                            });
                        } else {
                            // document.getElementById('notifikasi_error').play();
                            if(respond.notifikasi == "notifikasi_radius"){
                                document.getElementById('notifikasi_radus').play();
                            }
                            Swal.fire({
                                title: 'Gagal!',
                                text: respond.message,
                                icon: 'error'
                            });
                            btn.prop('disabled', false).removeClass('btn-loading').html(originalText);
                        }
                    },
                    error: function(xhr) {
                        // document.getElementById('notifikasi_error').play();
                        
                        var message = 'Terjadi kesalahan sistem.';
                        if (xhr.responseJSON) {
                            var respond = xhr.responseJSON;
                            message = respond.message;
                            if(respond.notifikasi == "notifikasi_radius"){
                                document.getElementById('notifikasi_radus').play();
                            }
                        }
                        
                        Swal.fire({
                                title: 'Gagal!',
                                text: message,
                                icon: 'error'
                        });
                        btn.prop('disabled', false).removeClass('btn-loading').html(originalText);
                    }
                });
            });
        });

        // Helper: Convert DataURI to Blob
        function dataURItoBlob(dataURI) {
            var byteString = atob(dataURI.split(',')[1]);
            var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
            var ab = new ArrayBuffer(byteString.length);
            var ia = new Uint8Array(ab);
            for (var i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }
            return new Blob([ab], { type: mimeString });
        }
    });
</script>
@endpush
