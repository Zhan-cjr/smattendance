@extends('layouts.mobile.app')
@section('content')
    <style>
        .webcam-capture {
            width: 100%;
            max-width: 98vw;
            height: 0;
            /* Diperkecil dari 133.33% ke 100% agar rasio kotak (1:1) dan hemat ruang */
            padding-top: 100%; 
            margin: 0 auto;
            padding: 0;
            border-radius: 24px;
            overflow: hidden;
            background: #222;
            position: relative;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.10);
            display: flex;
            align-items: center;
            justify-content: center;
            /* Batasi tinggi maksimal agar menu di bawah tetap muncul di layar kecil */
            max-height: 45vh; 
        }

        .webcam-capture video,
        .webcam-capture canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            border-radius: 24px !important;
            display: block;
        }

        #map {
            height: 200px;
            width: 100%;
            margin-bottom: 10px;
            opacity: 0.8;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        canvas {
            position: absolute;
            border-radius: 0;
            box-shadow: none;
        }

        #facedetection {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            height: 100%;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        #map-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 5px;
        }

        #header-section {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        #content-section {
            margin-top: 60px !important;
            padding: 0 !important;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .scan-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 0 10px;
        }

        #listcabang {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 20px;
            width: 92%;
            display: flex;
            justify-content: center;
            z-index: 20;
            margin-top: 0;
        }

        #listcabang .select-wrapper {
            position: relative;
            width: 90%;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
            }

            70% {
                box-shadow: 0 0 0 5px rgba(255, 255, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        #listcabang .select-wrapper::before {
            content: "";
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>');
            background-repeat: no-repeat;
            background-position: center;
            pointer-events: none;
        }

        #listcabang select {
            width: 100%;
            height: 45px;
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            padding: 0 15px 0 45px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        #listcabang select:hover {
            background-color: rgba(0, 0, 0, 0.6);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        #listcabang select:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.5);
            background-color: rgba(0, 0, 0, 0.6);
            animation: pulse 1.5s infinite;
        }

        #listcabang select option {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
        }

        #listcabang .select-wrapper::after {
            content: "";
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 12px;
            height: 12px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
            background-repeat: no-repeat;
            background-position: center;
            pointer-events: none;
        }

        .scan-button {
            height: 45px !important;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            width: 42%;
        }

        .scan-button ion-icon {
            margin-right: 5px;
        }

        .jam-digital-malasngoding {
            background-color: rgba(39, 39, 39, 0.7);
            position: absolute;
            top: 65px;
            right: 15px;
            z-index: 20;
            width: 150px;
            border-radius: 10px;
            padding: 5px;
            backdrop-filter: blur(5px);
        }

        .jam-digital-malasngoding p {
            color: #fff;
            font-size: 16px;
            text-align: left;
            margin-top: 0;
            margin-bottom: 0;
        }

        .face-detection-box {
            border: 2px solid #4CAF50;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
            transition: all 0.3s ease;
        }

        .face-detection-box.unknown {
            border-color: #F44336;
            box-shadow: 0 0 10px rgba(244, 67, 54, 0.5);
        }

        .face-detection-label {
            background-color: rgba(76, 175, 80, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .face-detection-label.unknown {
            background-color: rgba(244, 67, 54, 0.8);
        }

        .presensi-content-modern {
            background: linear-gradient(135deg, #e0f7fa 0%, #fff 100%);
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
            /* Padding diperkecil agar hemat ruang */
            padding: 10px 10px 15px 10px;
            margin: 5px 0;
            display: flex;
            flex-direction: column;
        }

        .presensi-content-modern,
        .presensi-content-modern * {
            font-family: 'Poppins', sans-serif !important;
        }

        .camera-section {
            padding: 2px;
            position: relative;
            flex-shrink: 0;
            margin-bottom: 10px; /* Diperkecil dari 20px */
        }

        .info-section {
            background: transparent;
            border-radius: 12px;
            padding: 10px 14px;
            margin-bottom: 8px;
            backdrop-filter: blur(6px);
            color: #222;
            font-size: 15px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex-grow: 1;
        }

        .info-section p {
            margin: 0;
            font-size: 15px;
        }

        .location-section {
            margin-bottom: 12px;
        }

        .map-section {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
            margin-bottom: 14px;
        }

        .action-section {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .action-section .scan-button {
            flex: 1;
            font-size: 18px;
            border-radius: 24px;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
            transition: transform 0.1s, box-shadow 0.1s;
        }

        .action-section .scan-button:active {
            transform: scale(0.97);
            box-shadow: 1px 4px rgba(44, 62, 80, 0.12);
        }

        .jadwalkerja-row {
            background: linear-gradient(90deg, #35796A 0%, #24584C 100%);
            border-radius: 16px;
            box-shadow: 0 4px 18px rgba(44, 62, 80, 0.13);
            margin-bottom: 6px;
            padding: 8px 0 4px 0;
            display: flex;
            justify-content: space-between;
            border: none;
            position: relative;
        }

        .jadwalkerja-col:not(:last-child) {
            border-right: 1.5px solid rgba(255, 255, 255, 0.22);
        }

        .jadwalkerja-col {
            padding: 0 6px;
        }

        .jadwalkerja-icon {
            font-size: 28px;
            color: #FFD600;
            margin-bottom: 2px;
        }

        .jadwalkerja-label {
            font-size: 13px;
            color: #fff;
            margin-bottom: 2px;
        }

        .jadwalkerja-value {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            letter-spacing: 1px;
        }

        .abs-tanggal-modern {
            position: absolute;
            top: 12px;
            left: 30px;
            background: rgba(255, 255, 255, 0.75);
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
            border-radius: 10px;
            padding: 4px 8px;
            font-size: 14px;
            font-weight: 600;
            color: #222;
            z-index: 10;
            backdrop-filter: blur(4px);
        }

        .abs-jam-modern {
            position: absolute;
            top: 12px;
            right: 30px;
            background: rgba(255, 255, 255, 0.75);
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
            border-radius: 10px;
            padding: 4px 8px;
            font-size: 14px;
            font-weight: 600;
            color: #222;
            z-index: 10;
            letter-spacing: 1px;
            backdrop-filter: blur(4px);
        }

        .map-absolute-section {
            position: absolute;
            bottom: 60px;
            width: 100%;
            z-index: 15;
            padding: 0 0 10px 0;
            display: flex;
            justify-content: center;
        }

        .map-absolute-section #map {
            /* Tinggi peta diperkecil agar tidak menutupi wajah saat liveness */
            height: 90px; 
            width: 80%;
            margin: 0 auto;
            opacity: 0.45;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="javascript:;" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">SM-Attendance</div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section">
        <div class="presensi-content-modern">
            <div class="camera-section" style="position:relative;">
                <div class="row" style="margin-top: 0;">
                    <div class="col" id="facedetection" style="position:relative;">
                        <div class="abs-tanggal-modern">{{ DateToIndo(date('Y-m-d')) }}</div>
                        <div class="abs-jam-modern"><span id="jam"></span></div>
                        <div class="webcam-capture"></div>
                        <input type="hidden" id="server-time" value="{{ date('Y-m-d H:i:s') }}">
                        <div class="map-absolute-section">
                            <div id="map">
                                <div id="map-loading">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <div class="mt-2" style="font-size:10px;">Memuat peta...</div>
                                </div>
                            </div>
                        </div>
                        <div id="listcabang">
                            <div class="select-wrapper">
                                <select name="cabang" id="cabang" class="form-control"
                                    {{ $karyawan->lock_location == 1 ? 'disabled' : '' }}>
                                    @foreach ($cabang as $item)
                                        @if ($karyawan->lock_location == 1)
                                            @if ($item->kode_cabang == $karyawan->kode_cabang)
                                                <option selected value="{{ $item->lokasi_cabang }}">
                                                    {{ $item->nama_cabang }}</option>
                                            @endif
                                        @else
                                            <option {{ $item->kode_cabang == $karyawan->kode_cabang ? 'selected' : '' }}
                                                value="{{ $item->lokasi_cabang }}">
                                                {{ $item->nama_cabang }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="info-section">
                <div class="row jadwalkerja-row">
                    <div class="col text-center jadwalkerja-col">
                        <ion-icon name="person-outline" class="jadwalkerja-icon"></ion-icon>
                        <div class="jadwalkerja-label">Shift</div>
                        <div class="jadwalkerja-value">{{ $jam_kerja->nama_jam_kerja }}</div>
                    </div>
                    <div class="col text-center jadwalkerja-col">
                        <ion-icon name="log-in-outline" class="jadwalkerja-icon"></ion-icon>
                        <div class="jadwalkerja-label">Jam Masuk</div>
                        <div class="jadwalkerja-value">{{ date('H:i', strtotime($jam_kerja->jam_masuk)) }}</div>
                    </div>
                    <div class="col text-center jadwalkerja-col">
                        <ion-icon name="log-out-outline" class="jadwalkerja-icon"></ion-icon>
                        <div class="jadwalkerja-label">Jam Pulang</div>
                        <div class="jadwalkerja-value">{{ date('H:i', strtotime($jam_kerja->jam_pulang)) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <audio id="notifikasi_radius">
        <source src="{{ asset('assets/sound/radius.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_mulaiabsen">
        <source src="{{ asset('assets/sound/mulaiabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_akhirabsen">
        <source src="{{ asset('assets/sound/akhirabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_sudahabsen">
        <source src="{{ asset('assets/sound/sudahabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_absenmasuk">
        <source src="{{ asset('assets/sound/absenmasuk.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_sudahabsenpulang">
        <source src="{{ asset('assets/sound/sudahabsenpulang.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_absenpulang">
        <source src="{{ asset('assets/sound/absenpulang.mp3') }}" type="audio/mpeg">
    </audio>
@endsection

@push('myscript')
    <script type="text/javascript">
        window.onload = function() {
            startServerClock();
        }

        function startServerClock() {
            const serverTimeStr = document.getElementById('server-time').value;
            const serverDate = new Date(serverTimeStr.replace(/-/g, '/'));
            const localDate = new Date();
            const offset = localDate.getTime() - serverDate.getTime();

            setInterval(function() {
                const now = new Date(new Date().getTime() - offset);
                const hours = now.getHours();
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                
                const jamElement = document.getElementById('jam');
                if (jamElement) {
                    jamElement.innerHTML = `${hours}:${minutes}:${seconds}`;
                }
            }, 1000);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        // Deklarasi variabel global
        let faceRecognitionDetected = 0;
        let livenessPassed = false;
        let lokasi;
        let isSpeaking = false;
        let faceVerified = false;
        let selectedAbsenType = null;
        let isAttendanceSubmitted = false;
        let mapLoaded = false;
        let isMapLoadingSpoken = false;
        let userLatitude = null;
        let userLongitude = null;
        let userAccuracy = null;
        let currentMap;
        let userMarker;
        let officeCircle;

        // Variabel untuk liveness detection
        let isLivenessActive = false;
        let livenessInstructionIndex = 0;
        let livenessInstructions = ["tengok kiri", "tengok kanan"];
        let livenessStatus = "";
        let consecutiveMovementDetections = 0;
        let isMovementDetected = false;

        function speakInstruction(text) {
            if (!('speechSynthesis' in window)) {
                console.error('Web Speech API tidak didukung.');
                return;
            }

            if (isSpeaking) {
                window.speechSynthesis.cancel();
            }

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.8;
            utterance.pitch = 1.0;
            utterance.volume = 1.0;

            const voices = window.speechSynthesis.getVoices();
            const indonesianVoice = voices.find(voice => voice.lang.includes('id-ID'));
            if (indonesianVoice) {
                utterance.voice = indonesianVoice;
            }

            utterance.onstart = () => { isSpeaking = true; };
            utterance.onend = () => { isSpeaking = false; };
            utterance.onerror = (event) => { isSpeaking = false; console.error('Error dalam speech synthesis:', event.error); };

            try {
                window.speechSynthesis.speak(utterance);
            } catch (error) {
                console.error('Error saat memulai speech synthesis:', error);
            }
        }
        
        function stopAllAudio() {
            window.speechSynthesis.cancel();
            document.querySelectorAll('audio').forEach(audio => {
                audio.pause();
                audio.currentTime = 0;
            });
        }
        
        function haversineDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const φ1 = lat1 * Math.PI / 180;
            const φ2 = lat2 * Math.PI / 180;
            const Δφ = (lat2 - lat1) * Math.PI / 180;
            const Δλ = (lon2 - lon1) * Math.PI / 180;

            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c;
        }

        $(function() {
            Swal.fire({
                title: 'Pilih Tipe Absensi',
                text: "Silakan pilih apakah Anda ingin Absen Masuk atau Absen Pulang.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Absen Masuk',
                cancelButtonText: 'Absen Pulang',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    selectedAbsenType = '1';
                    speakInstruction("Anda memilih Absen Masuk. Mohon tunggu proses verifikasi wajah dan lokasi.");
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    selectedAbsenType = '2';
                    speakInstruction("Anda memilih Absen Pulang. Mohon tunggu proses verifikasi wajah dan lokasi.");
                } else {
                    window.location.href = '/dashboard';
                }
                
                initAttendanceProcess();
            });

            function initAttendanceProcess() {
                let multi_lokasi = {{ $general_setting->multi_lokasi }};
                let lokasi_cabang = multi_lokasi ? document.getElementById('cabang').value : "{{ $lokasi_kantor->lokasi_cabang }}";
                let is_niqab = {{ $karyawan->is_niqab ?? 0 }};
                
                document.querySelectorAll('audio').forEach(audio => {
                    audio.muted = true;
                });
                
                let faceRecognition = "{{ $general_setting->face_recognition }}";
                const isMobile = /Android|webOS|iPhone|iPad|IEMobile|Opera Mini/i.test(navigator.userAgent);

                function initWebcam() {
                    Webcam.set({
                        width: 480,
                        height: 640,
                        image_format: 'jpeg',
                        jpeg_quality: isMobile ? 80 : 95,
                        fps: isMobile ? 15 : 30,
                        constraints: {
                            video: {
                                width: { ideal: isMobile ? 240 : 480 },
                                height: { ideal: isMobile ? 320 : 640 },
                                facingMode: "user",
                                frameRate: { ideal: isMobile ? 15 : 30 }
                            }
                        }
                    });
                    Webcam.attach('.webcam-capture');
                    
                    Webcam.on('live', function() {
                        speakInstruction("Kamera siap. Harap arahkan wajah Anda untuk verifikasi. Pastikan pencahayaan cukup dan wajah terlihat jelas.");
                    });
                }

                initWebcam();

                document.addEventListener('visibilitychange', function() {
                    if (document.visibilityState === 'visible') {
                        if (!Webcam.isInitialized()) {
                            initWebcam();
                        }
                    }
                });

                function loadMapAndGeolocation(locationData) {
                    if (navigator.geolocation) {
                        if(userLatitude && userLongitude){
                            updateMap(userLatitude, userLongitude, userAccuracy, locationData);
                        } else {
                            navigator.geolocation.getCurrentPosition(
                                (position) => successCallback(position, locationData),
                                (error) => errorCallback(error, locationData),
                                { timeout: 10000, enableHighAccuracy: true }
                            );
                        }
                    } else {
                        mapLoaded = true;
                        document.getElementById('map-loading').innerHTML = 'Geolokasi tidak didukung oleh perangkat ini.';
                        if (is_niqab == 1) {
                            setTimeout(() => {
                                handleAbsen(selectedAbsenType);
                            }, 1000);
                        }
                    }
                }

                function updateMap(latUser, lonUser, accuracyUser, locationData) {
                    try {
                        if (currentMap) {
                            currentMap.off();
                            currentMap.remove();
                        }

                        currentMap = L.map('map').setView([latUser, lonUser], 18);
                        lokasi = `${latUser},${lonUser},${accuracyUser}`;
                        var lok = locationData.split(",");
                        var lat_kantor = parseFloat(lok[0]);
                        var long_kantor = parseFloat(lok[1]);
                        var radius = "{{ $lokasi_kantor->radius_cabang }}";
                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                        }).addTo(currentMap);
                        
                        userMarker = L.marker([latUser, lonUser]).addTo(currentMap);
                        officeCircle = L.circle([lat_kantor, long_kantor], {
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.5,
                            radius: radius
                        }).addTo(currentMap);
                        
                        document.getElementById('map-loading').style.display = 'none';
                        setTimeout(() => {
                            currentMap.invalidateSize();
                            mapLoaded = true;
                            isMapLoadingSpoken = false;

                            if (is_niqab == 1) {
                                if (!isAttendanceSubmitted) {
                                    isAttendanceSubmitted = true;
                                    stopAllAudio();
                                    speakInstruction("Verifikasi berhasil. Memproses absensi Anda.");
                                    setTimeout(() => {
                                        handleAbsen(selectedAbsenType);
                                    }, 1500);
                                }
                            }
                        }, 500);
                    } catch (error) {
                        document.getElementById('map-loading').innerHTML = 'Gagal memuat peta. Silakan coba lagi.';
                    }
                }

                function successCallback(position, locationData) {
                    userLatitude = position.coords.latitude;
                    userLongitude = position.coords.longitude;
                    userAccuracy = position.coords.accuracy;
                    
                    updateMap(userLatitude, userLongitude, userAccuracy, locationData);
                }

                function errorCallback(error, locationData) {
                    if (currentMap) currentMap.remove();
                    document.getElementById('map-loading').innerHTML = 'Gagal mendapatkan lokasi. Silakan cek izin lokasi.';
                    mapLoaded = true;
                    isMapLoadingSpoken = false;
                    try {
                        var lok = locationData.split(",");
                        var lat_kantor = parseFloat(lok[0]);
                        var long_kantor = parseFloat(lok[1]);
                        currentMap = L.map('map').setView([lat_kantor, long_kantor], 18);
                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                        }).addTo(currentMap);
                        var radius = "{{ $lokasi_kantor->radius_cabang }}";
                        L.circle([lat_kantor, long_kantor], {
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.5,
                            radius: radius
                        }).addTo(currentMap);
                        document.getElementById('map-loading').style.display = 'none';

                        if (is_niqab == 1) {
                            if (!isAttendanceSubmitted) {
                                isAttendanceSubmitted = true;
                                stopAllAudio();
                                speakInstruction("Verifikasi berhasil. Memproses absensi Anda.");
                                setTimeout(() => {
                                    handleAbsen(selectedAbsenType);
                                }, 1500);
                            }
                        }
                    } catch (mapError) {
                        document.getElementById('map-loading').style.display = 'none';
                    }
                }

                if (faceRecognition == 1 && is_niqab == 0) {
                    const loadingIndicator = document.createElement('div');
                    loadingIndicator.id = 'face-recognition-loading';
                    loadingIndicator.innerHTML = `<div class="spinner-border text-light" role="status"><span class="sr-only">Memuat pengenalan wajah...</span></div><div class="mt-2 text-light">Memuat model pengenalan wajah...</div>`;
                    loadingIndicator.style.position = 'absolute';
                    loadingIndicator.style.top = '50%';
                    loadingIndicator.style.left = '50%';
                    loadingIndicator.style.transform = 'translate(-50%, -50%)';
                    loadingIndicator.style.zIndex = '1000';
                    loadingIndicator.style.textAlign = 'center';
                    document.getElementById('facedetection').appendChild(loadingIndicator);
                    
                    speakInstruction("Memuat model pengenalan wajah. Mohon tunggu.");

                    const modelLoadingPromise = isMobile ? Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                        faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                        faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    ]) : Promise.all([
                        faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
                        faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                        faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    ]);

                    modelLoadingPromise.then(() => {
                        document.getElementById('face-recognition-loading').remove();
                        const video = document.querySelector('.webcam-capture video');
                        if (video) {
                            video.addEventListener('loadedmetadata', () => {});
                            video.addEventListener('canplay', () => {});
                            video.addEventListener('playing', () => {});
                            video.addEventListener('error', (e) => {});
                        }
                        startFaceRecognition();
                    }).catch(err => {
                        document.getElementById('face-recognition-loading').remove();
                        setTimeout(() => {
                            modelLoadingPromise.then(() => {
                                startFaceRecognition();
                            });
                        }, 2000);
                    });

                    async function getLabeledFaceDescriptions() {
                        const labels = ["{{ $karyawan->nik }}-{{ getNamaDepan(strtolower($karyawan->nama_karyawan)) }}"];
                        let namakaryawan;
                        let jmlwajah = "{{ $wajah == 0 ? 1 : $wajah }}";
                        const faceDataLoading = document.createElement('div');
                        faceDataLoading.id = 'face-data-loading';
                        faceDataLoading.innerHTML = `<div class="spinner-border text-light" role="status"><span class="sr-only">Memuat data wajah...</span></div><div class="mt-2 text-light">Memuat data wajah...</div>`;
                        faceDataLoading.style.position = 'absolute';
                        faceDataLoading.style.top = '50%';
                        faceDataLoading.style.left = '50%';
                        faceDataLoading.style.transform = 'translate(-50%, -50%)';
                        faceDataLoading.style.zIndex = '1000';
                        faceDataLoading.style.textAlign = 'center';
                        document.getElementById('facedetection').appendChild(faceDataLoading);

                        try {
                            const timestamp = new Date().getTime();
                            const response = await fetch(`/facerecognition/getwajah?t=${timestamp}`);
                            const data = await response.json();
                            const result = await Promise.all(
                                labels.map(async (label) => {
                                    const descriptions = [];
                                    let validFaceFound = false;
                                    for (const faceData of data.slice(0, 5)) {
                                        const checkImage = async (label, wajahFile) => {
                                            try {
                                                const imagePath = `/storage/uploads/facerecognition/${label}/${wajahFile}?t=${timestamp}`;
                                                const response = await fetch(imagePath);
                                                if (!response.ok) {
                                                    return null;
                                                }
                                                return await faceapi.fetchImage(imagePath);
                                            } catch (err) {
                                                return null;
                                            }
                                        };
                                        const img = await checkImage(label, faceData.wajah);
                                        if (img) {
                                            try {
                                                let detections;
                                                if (isMobile) {
                                                    detections = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({
                                                        inputSize: 160,
                                                        scoreThreshold: 0.5
                                                    })).withFaceLandmarks().withFaceDescriptor();
                                                } else {
                                                    detections = await faceapi.detectSingleFace(img, new faceapi.SsdMobilenetv1Options({
                                                        minConfidence: 0.5
                                                    })).withFaceLandmarks().withFaceDescriptor();
                                                }
                                                if (detections) {
                                                    descriptions.push(detections.descriptor);
                                                    validFaceFound = true;
                                                }
                                            } catch (err) {}
                                        }
                                    }
                                    if (!validFaceFound) {
                                        namakaryawan = "unknown";
                                    } else {
                                        namakaryawan = label;
                                    }
                                    return new faceapi.LabeledFaceDescriptors(namakaryawan, descriptions);
                                })
                            );
                            document.getElementById('face-data-loading').remove();
                            return result;
                        } catch (error) {
                            document.getElementById('face-data-loading').remove();
                            throw error;
                        }
                    }

                    async function startFaceRecognition() {
                        try {
                            const labeledFaceDescriptors = await getLabeledFaceDescriptions();
                            const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.5);
                            const video = document.querySelector('.webcam-capture video');
                            if (!video) {
                                setTimeout(startFaceRecognition, 1000);
                                return;
                            }
                            if (!video.videoWidth || !video.videoHeight || video.readyState < 2) {
                                setTimeout(startFaceRecognition, 500);
                                return;
                            }

                            const parent = video.parentElement;
                            if (!parent) {
                                return;
                            }

                            const existingCanvas = parent.querySelector('canvas');
                            if (existingCanvas) {
                                existingCanvas.remove();
                            }

                            const canvas = faceapi.createCanvasFromMedia(video);
                            await new Promise(resolve => setTimeout(resolve, 100));
                            const videoWidth = video.videoWidth;
                            const videoHeight = video.videoHeight;
                            canvas.width = videoWidth;
                            canvas.height = videoHeight;
                            canvas.style.position = 'absolute';
                            canvas.style.top = '0';
                            canvas.style.left = '0';
                            canvas.style.width = '100%';
                            canvas.style.height = '100%';
                            canvas.style.pointerEvents = 'none';
                            canvas.style.zIndex = '10';
                            const videoStyle = window.getComputedStyle(video);
                            if (videoStyle.transform.includes('matrix(-1')) {
                                canvas.style.transform = 'scaleX(-1)';
                            }
                            parent.appendChild(canvas);
                            const ctx = canvas.getContext("2d");
                            if (!ctx) {
                                return;
                            }
                            const displaySize = {
                                width: videoWidth,
                                height: videoHeight
                            };
                            faceapi.matchDimensions(canvas, displaySize);

                            let lastDetectionTime = 0;
                            let detectionInterval = isMobile ? 400 : 100;
                            let isProcessing = false;
                            let consecutiveMatches = 0;
                            const requiredConsecutiveMatches = isMobile ? 2 : 4;

                            let lastInstructionTime = 0;
                            const instructionCooldown = 3000;
                            let lastMovementTime = 0;
                            let isPerformingLiveness = false;
                            const movementPause = 2000;
                            
                            let previousYaw = null;
                            const yawThreshold = 0.15;

                            function detectHeadMovement(landmarks) {
                                if (!landmarks || landmarks.length < 68) return;
                                
                                const nose = landmarks[30];
                                const leftEye = landmarks[36];
                                const rightEye = landmarks[45];
                                
                                let currentMovement = "diam";
                                const faceWidth = Math.abs(rightEye.x - leftEye.x);
                                
                                const normalizedNoseX = (nose.x - (leftEye.x + rightEye.x) / 2) / faceWidth;
                                
                                if (normalizedNoseX > yawThreshold) {
                                    currentMovement = "tengok kiri";
                                } else if (normalizedNoseX < -yawThreshold) {
                                    currentMovement = "tengok kanan";
                                }
                                
                                const requiredMovement = livenessInstructions[livenessInstructionIndex];
                                
                                if (currentMovement === requiredMovement && !isMovementDetected) {
                                    isMovementDetected = true;
                                    consecutiveMovementDetections++;
                                    if (consecutiveMovementDetections >= 1) { 
                                        livenessInstructionIndex++;
                                        consecutiveMovementDetections = 0;
                                        isMovementDetected = false;

                                        if (livenessInstructionIndex < livenessInstructions.length) {
                                            speakInstruction(`Bagus. Sekarang ${livenessInstructions[livenessInstructionIndex]}`);
                                        } else {
                                            livenessPassed = true;
                                            isLivenessActive = false;
                                            speakInstruction("Verifikasi liveness berhasil.");
                                        }
                                    }
                                } else if (currentMovement !== requiredMovement) {
                                    isMovementDetected = false;
                                }
                            }

                            function updateCanvas() {
                                if (!video || !canvas || !ctx) return;
                                if (!video.videoWidth || !video.videoHeight) {
                                    setTimeout(updateCanvas, 500);
                                    return;
                                }
                                if (!isProcessing && !isAttendanceSubmitted) {
                                    const now = Date.now();
                                    if (now - lastDetectionTime > detectionInterval) {
                                        isProcessing = true;
                                        lastDetectionTime = now;
                                        faceapi.detectSingleFace(video, isMobile ? new faceapi.TinyFaceDetectorOptions({
                                            inputSize: 160,
                                            scoreThreshold: 0.4
                                        }) : new faceapi.SsdMobilenetv1Options({
                                            minConfidence: 0.5
                                        })).withFaceLandmarks().withFaceDescriptor().then(detection => {
                                            const detections = detection ? [detection] : [];
                                            const resizedDetections = faceapi.resizeResults(detections, displaySize);
                                            ctx.clearRect(0, 0, canvas.width, canvas.height);

                                            let hasFace = resizedDetections && resizedDetections.length > 0;
                                            let detectedFace = hasFace ? resizedDetections[0] : null;

                                            let boxColor, labelColor, labelText;

                                            if (hasFace && detectedFace.descriptor) {
                                                const match = faceMatcher.findBestMatch(detectedFace.descriptor);
                                                const box = detectedFace.detection.box;
                                                const isUnknown = match.toString().includes("unknown");
                                                const isNotRecognized = match.distance > 0.55;

                                                if (isUnknown || isNotRecognized) {
                                                    boxColor = '#FFC107';
                                                    labelColor = 'rgba(255, 193, 7, 0.8)';
                                                    labelText = 'Wajah Tidak Dikenali';

                                                    consecutiveMatches = 0;
                                                    faceRecognitionDetected = 0;
                                                    livenessPassed = false;
                                                    isLivenessActive = false;
                                                    livenessInstructionIndex = 0;
                                                    consecutiveMovementDetections = 0;
                                                    isMovementDetected = false;
                                                    faceVerified = false; 
                                                    if (!isSpeaking) {
                                                        speakInstruction("Wajah tidak dikenali. Silakan coba lagi.");
                                                    }
                                                } else {
                                                    consecutiveMatches++;
                                                    if (consecutiveMatches >= requiredConsecutiveMatches) {
                                                        faceRecognitionDetected = 1;
                                                        boxColor = '#4CAF50';
                                                        labelColor = 'rgba(76, 175, 80, 0.8)';
                                                        labelText = "{{ $karyawan->nama_karyawan }}";
                                                        
                                                        if (!faceVerified && !livenessPassed) {
                                                            speakInstruction(`Wajah Anda terdeteksi. Silakan ${livenessInstructions[livenessInstructionIndex]}`);
                                                            faceVerified = true;
                                                            isLivenessActive = true;
                                                        }
                                                        
                                                        if (detectedFace.landmarks && isLivenessActive && !livenessPassed) {
                                                            detectHeadMovement(detectedFace.landmarks.positions);
                                                        }
                                                    } else {
                                                        faceRecognitionDetected = 0;
                                                        boxColor = '#4CAF50';
                                                        labelColor = 'rgba(76, 175, 80, 0.8)';
                                                        labelText = `Verifikasi Wajah... (${consecutiveMatches}/${requiredConsecutiveMatches})`;
                                                        
                                                        livenessPassed = false;
                                                        isLivenessActive = false;
                                                        livenessInstructionIndex = 0;
                                                        consecutiveMovementDetections = 0;
                                                    }
                                                }

                                                ctx.strokeStyle = boxColor;
                                                ctx.lineWidth = 3;
                                                ctx.strokeRect(box.x, box.y, box.width, box.height);
                                                ctx.font = "bold 14px Arial";
                                                ctx.fillStyle = labelColor;
                                                ctx.fillRect(box.x, box.y + box.height + 4, box.width, 22);
                                                ctx.fillStyle = "#fff";
                                                ctx.textAlign = "center";
                                                ctx.fillText(labelText, box.x + box.width / 2, box.y + box.height + 18);
                                            } else {
                                                ctx.font = "bold 22px Arial";
                                                ctx.textAlign = "center";
                                                ctx.fillStyle = "#F44336";
                                                ctx.fillText("Wajah Tidak Terdeteksi", canvas.width / 2, canvas.height / 2);

                                                consecutiveMatches = 0;
                                                faceRecognitionDetected = 0;
                                                livenessPassed = false;
                                                isLivenessActive = false;
                                                livenessInstructionIndex = 0;
                                                consecutiveMovementDetections = 0;
                                                isMovementDetected = false;
                                                faceVerified = false;
                                                if (!isSpeaking) {
                                                    speakInstruction("Harap arahkan wajah Anda ke kamera.");
                                                }
                                            }

                                            ctx.save();
                                            let yStatus = 80;
                                            ctx.font = "bold 15px Poppins, Arial";
                                            ctx.textAlign = "center";
                                            ctx.textBaseline = "middle";
                                            ctx.globalAlpha = 0.92;
                                            ctx.fillStyle = "#222";
                                            ctx.fillRect(canvas.width / 2 - 130, yStatus - 16, 260, 32);
                                            ctx.globalAlpha = 1;
                                            if (faceRecognitionDetected == 1) {
                                                if (livenessPassed) {
                                                    ctx.fillStyle = "#4CAF50";
                                                    ctx.fillText("Liveness terverifikasi!", canvas.width / 2, yStatus);
                                                } else {
                                                    ctx.fillStyle = "#FFD600";
                                                    livenessStatus = `Silakan ${livenessInstructions[livenessInstructionIndex]}`;
                                                    ctx.fillText(
                                                        `Status: ${livenessStatus} (${livenessInstructionIndex + 1}/${livenessInstructions.length})`,
                                                        canvas.width / 2,
                                                        yStatus
                                                    );
                                                }
                                            } else {
                                                ctx.fillStyle = "#FFD600";
                                                ctx.fillText("Menunggu pengenalan wajah...", canvas.width / 2, yStatus);
                                            }

                                            if (faceRecognitionDetected && livenessPassed && selectedAbsenType) {
                                                if (!mapLoaded) {
                                                    if (!isMapLoadingSpoken) {
                                                        speakInstruction("Peta belum selesai dimuat. Mohon tunggu.");
                                                        isMapLoadingSpoken = true;
                                                    }
                                                } else if (!isAttendanceSubmitted) {
                                                    isAttendanceSubmitted = true;
                                                    stopAllAudio();
                                                    
                                                    // PERUBAHAN DI SINI: Berikan jeda 3 detik agar posisi wajah kembali ke depan
                                                    speakInstruction("Verifikasi berhasil. Mohon hadap depan, foto akan diambil dalam tiga detik.");
                                                    
                                                    setTimeout(() => {
                                                        handleAbsen(selectedAbsenType);
                                                    }, 3000); // Delay 3 detik sesuai permintaan
                                                }
                                            }
                                            isProcessing = false;
                                        }).catch(err => {
                                            isProcessing = false;
                                        });
                                    }
                                }
                                if (isMobile) {
                                    setTimeout(updateCanvas, detectionInterval);
                                } else {
                                    requestAnimationFrame(updateCanvas);
                                }
                            }
                            updateCanvas();
                        } catch (error) {
                            setTimeout(() => {
                                startFaceRecognition();
                            }, 2000);
                        }
                    }
                } else {
                    faceRecognitionDetected = 1;
                    livenessPassed = true;
                    faceVerified = true;
                }

                const takePhoto = () => {
                    return new Promise((resolve, reject) => {
                        Webcam.snap(uri => {
                            if (uri) {
                                resolve(uri);
                            } else {
                                reject('Failed to take photo.');
                            }
                        });
                    });
                };

                async function handleAbsen(status) {
                    const [officeLat, officeLon] = lokasi_cabang.split(',').map(parseFloat);
                    const officeRadius = parseFloat("{{ $lokasi_kantor->radius_cabang }}");

                    lokasi = `${userLatitude},${userLongitude},${userAccuracy}`;

                    const distance = haversineDistance(userLatitude, userLongitude, officeLat, officeLon);
                    if (distance > officeRadius) {
                        stopAllAudio();
                        speakInstruction("Anda berada di luar area yang diizinkan.");
                         swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Lokasi Anda berada di luar area yang diizinkan.',
                        }).then(() => {
                            window.location.href = '/dashboard';
                        });
                        return;
                    }

                    if (userAccuracy < 5) {
                        stopAllAudio();
                        speakInstruction("Lokasi Anda tidak valid. Terdeteksi memakai Fake GPS.");
                         swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Lokasi Anda tidak valid. Terdeteksi memakai Fake GPS.',
                        }).then(() => {
                            window.location.href = '/dashboard';
                        });
                        return;
                    }

                    try {
                        const capturedImageUri = await takePhoto();
                        const finalImageUri = await processImage(capturedImageUri, status);

                        $.ajax({
                            type: 'POST',
                            url: "{{ route('presensi.store') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                image: finalImageUri,
                                status: status,
                                lokasi: lokasi,
                                lokasi_cabang: lokasi_cabang,
                                kode_jam_kerja: "{{ $jam_kerja->kode_jam_kerja }}"
                            },
                            success: function(data) {
                                if (data.status == true) {
                                    stopAllAudio();
                                    if (status === '1') {
                                        speakInstruction("Absen masuk berhasil. Terima kasih.");
                                    } else {
                                        speakInstruction("Absen pulang berhasil. Terima kasih.");
                                    }
                                    saveImageToGallery(finalImageUri);
                                    swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: data.message,
                                        showConfirmButton: false,
                                        timer: 4000
                                    }).then(() => {
                                        window.location.href = '/dashboard';
                                    });
                                }
                            },
                            error: function(xhr) {
                                const message = xhr.responseJSON.message;
                                stopAllAudio();
                                speakInstruction(message);
                                swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: message,
                                }).then(() => {
                                    window.location.href = '/dashboard';
                                });
                            }
                        });
                    } catch (error) {
                        stopAllAudio();
                        speakInstruction("Terjadi kesalahan saat mengambil foto. Silakan coba lagi.");
                        swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error
                        }).then(() => {
                            window.location.href = '/dashboard';
                        });
                    }
                }
                
                function processImage(imageUri, status) {
                    return new Promise((resolve, reject) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            
                            const sourceWidth = img.width;
                            const sourceHeight = img.height;

                            canvas.width = sourceWidth;
                            canvas.height = sourceHeight;
                            
                            ctx.drawImage(img, 0, 0, sourceWidth, sourceHeight);

                            const absenType = status === '1' ? 'Absen Masuk' : 'Absen Pulang';
                            const watermarkColor = status === '1' ? 'rgba(40, 167, 69, 0.8)' : 'rgba(220, 53, 69, 0.8)';
                            const textColor = '#FFFFFF';
                            
                            const titleFontSize = sourceHeight * 0.035;
                            const textRectPadding = sourceWidth * 0.02;
                            const textRectRadius = 8;
                            const textRectX = sourceWidth * 0.02;
                            const textRectY = sourceWidth * 0.02;

                            ctx.fillStyle = watermarkColor;
                            ctx.font = `bold ${titleFontSize}px Poppins, Arial`;
                            const titleTextMetrics = ctx.measureText(absenType);
                            const titleTextWidth = titleTextMetrics.width + textRectPadding * 2;
                            const titleTextHeight = titleFontSize + textRectPadding;

                            ctx.beginPath();
                            ctx.moveTo(textRectX + textRectRadius, textRectY);
                            ctx.lineTo(textRectX + titleTextWidth - textRectRadius, textRectY);
                            ctx.quadraticCurveTo(textRectX + titleTextWidth, textRectY, textRectX + titleTextWidth, textRectY + textRectRadius);
                            ctx.lineTo(textRectX + titleTextWidth, textRectY + titleTextHeight - textRectRadius);
                            ctx.quadraticCurveTo(textRectX + titleTextWidth, textRectY + titleTextHeight, textRectX + titleTextWidth - textRectRadius, textRectY + titleTextHeight);
                            ctx.lineTo(textRectX + textRectRadius, textRectY + titleTextHeight);
                            ctx.quadraticCurveTo(textRectX, textRectY + titleTextHeight, textRectX, textRectY + titleTextHeight - textRectRadius);
                            ctx.lineTo(textRectX, textRectY + textRectRadius);
                            ctx.quadraticCurveTo(textRectX, textRectY, textRectX + textRectRadius, textRectY);
                            ctx.closePath();
                            ctx.fill();
                            
                            ctx.fillStyle = textColor;
                            ctx.fillText(absenType, textRectX + textRectPadding, textRectY + titleFontSize);

                            const iconSize = sourceHeight * 0.03;
                            const iconPadding = sourceWidth * 0.02;
                            const margin = sourceWidth * 0.04;
                            
                            const jamY = sourceHeight * 0.77; 
                            const tanggalY = jamY + (sourceHeight * 0.04); 
                            const lokasiY = tanggalY + (sourceHeight * 0.04); 
                            const namaKaryawanY = lokasiY + (sourceHeight * 0.04); 

                            const imgJam = new Image();
                            imgJam.onload = () => {
                                const jam = document.getElementById('jam').innerText;
                                const tanggal = '{{ DateToIndo(date('Y-m-d')) }}';
                                
                                ctx.drawImage(imgJam, margin, jamY, iconSize, iconSize);
                                ctx.font = `bold ${sourceHeight * 0.05}px Poppins, Arial`;
                                ctx.fillStyle = textColor;
                                ctx.textAlign = 'left';
                                ctx.fillText(jam, margin + iconSize + iconPadding, jamY + iconSize);

                                ctx.font = `bold ${sourceHeight * 0.03}px Poppins, Arial`;
                                ctx.fillText(tanggal, margin + iconSize + iconPadding, tanggalY + iconSize);

                                const imgLokasi = new Image();
                                imgLokasi.onload = () => {
                                    ctx.drawImage(imgLokasi, margin, lokasiY, iconSize, iconSize);
                                    const branchName = $("#cabang option:selected").text().trim();
                                    ctx.font = `bold ${sourceHeight * 0.03}px Poppins, Arial`;
                                    ctx.fillText(branchName, margin + iconSize + iconPadding, lokasiY + iconSize);
                                    
                                    const imgUser = new Image();
                                    imgUser.onload = () => {
                                        ctx.drawImage(imgUser, margin, namaKaryawanY, iconSize, iconSize);
                                        ctx.fillText('{{ $karyawan->nama_karyawan }}', margin + iconSize + iconPadding, namaKaryawanY + iconSize);

                                        const footerText = 'Waktu dan Lokasi diverifikasi oleh SM Attendance';
                                        ctx.font = `italic ${sourceHeight * 0.02}px Poppins, Arial`;
                                        ctx.fillStyle = textColor;
                                        ctx.textAlign = 'right';
                                        ctx.fillText(footerText, sourceWidth - margin, sourceHeight - margin);

                                        resolve(canvas.toDataURL('image/jpeg', 0.9));
                                    };
                                    imgUser.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';
                                };
                                imgLokasi.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>';
                            };
                            imgJam.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
                        };
                        img.onerror = reject;
                        img.src = imageUri;
                    });
                }
                
                function saveImageToGallery(imageUri) {
                    const link = document.createElement('a');
                    link.href = imageUri;
                    link.download = `Absen_${selectedAbsenType === '1' ? 'Masuk' : 'Pulang'}_${new Date().toISOString().slice(0, 10).replace(/-/g, '')}_${new Date().toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/:/g, '')}.jpg`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

                $("#cabang").change(function() {
                    lokasi_cabang = $(this).val();
                    let cabangText = $("#cabang option:selected").text().trim();
                    
                    speakInstruction(`Lokasi cabang berubah menjadi ${cabangText}. Peta sedang dimuat. Mohon tunggu.`);
                    swal.fire({
                        icon: 'info',
                        title: 'Lokasi Berubah',
                        text: 'Peta sedang diperbarui untuk lokasi baru.',
                        showConfirmButton: false,
                        timer: 2000
                    });

                    if (userLatitude && userLongitude) {
                        mapLoaded = false;
                        isMapLoadingSpoken = false;
                        updateMap(userLatitude, userLongitude, userAccuracy, lokasi_cabang);
                    } else {
                        mapLoaded = false;
                        isMapLoadingSpoken = false;
                        loadMapAndGeolocation(lokasi_cabang);
                    }
                });

                loadMapAndGeolocation(lokasi_cabang);
            }
        });
    </script>
@endpush