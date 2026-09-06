@extends('layouts.mobile.app')
@section('content')
    <script>
        (function() {
            var savedTheme = localStorage.getItem('smatt_theme') || (localStorage.getItem('MobilekitDarkModeActive') === '1' ? 'dark' : null);
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark', 'dark-mode-active');
                if (document.body) {
                    document.body.classList.add('dark', 'dark-mode-active');
                }
            }
        })();
    </script>
    <style>
        :root {
            --primary-color: {{ $t['primary'] ?? '#0f766e' }};
            --primary-dark: #115e59;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: #f8fafc;
            -webkit-tap-highlight-color: transparent;
        }

        /* Comprehensive Dark Mode Support */
        body.dark, body.dark-mode-active, html.dark body, html.dark-mode-active body {
            background-color: #0b1120 !important;
            color: #f8fafc !important;
        }

        body.dark #appCapsule, body.dark-mode-active #appCapsule,
        body.dark #content-section, body.dark-mode-active #content-section {
            background-color: transparent !important;
        }

        body.dark .presensi-content-modern,
        body.dark-mode-active .presensi-content-modern,
        .dark .presensi-content-modern {
            background: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
        }

        body.dark #liveness-status-section,
        body.dark-mode-active #liveness-status-section,
        .dark #liveness-status-section {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3) !important;
        }

        body.dark #liveness-status-text,
        body.dark-mode-active #liveness-status-text,
        .dark #liveness-status-text {
            color: #38bdf8 !important;
        }

        .webcam-capture {
            width: 100%;
            max-width: 98vw;
            height: 0;
            padding-top: 100%; 
            margin: 0 auto;
            padding: 0;
            border-radius: 28px;
            overflow: hidden;
            background: #0f172a;
            position: relative;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
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
            border-radius: 28px !important;
            display: block;
        }

        #map {
            height: 200px;
            width: 100%;
            margin-bottom: 10px;
            opacity: 0.8;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
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
            background-color: rgba(15, 23, 42, 0.85);
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 12px;
            backdrop-filter: blur(8px);
        }

        #header-section {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        #content-section {
            margin-top: 56px !important;
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
            bottom: 16px;
            width: 90%;
            display: flex;
            justify-content: center;
            z-index: 20;
            margin-top: 0;
        }

        #listcabang .select-wrapper {
            position: relative;
            width: 100%;
            animation: fadeIn 0.4s ease-in-out;
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

        #listcabang select {
            width: 100%;
            height: 42px;
            border-radius: 14px;
            background-color: rgba(15, 23, 42, 0.75);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 0 15px 0 38px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            transition: all 0.25s ease;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        #listcabang select:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
        }

        #listcabang select option {
            background-color: #0f172a;
            color: white;
        }

        #listcabang .select-wrapper::before {
            content: "📍";
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            pointer-events: none;
            z-index: 2;
        }

        #listcabang .select-wrapper::after {
            content: "▼";
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 9px;
            color: rgba(255, 255, 255, 0.7);
            pointer-events: none;
            z-index: 2;
        }

        .face-detection-box {
            border: 2.5px solid #10b981;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.6);
            transition: all 0.25s ease;
        }

        .face-detection-box.unknown {
            border-color: #ef4444;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.6);
        }

        .face-detection-label {
            background-color: rgba(16, 185, 129, 0.9);
            color: white;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(4px);
        }

        .face-detection-label.unknown {
            background-color: rgba(239, 68, 68, 0.9);
        }

        .presensi-content-modern {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.06);
            border: 1px solid #e2e8f0;
            padding: 10px 10px 14px 10px;
            margin: 6px 0;
            display: flex;
            flex-direction: column;
        }

        .camera-section {
            padding: 2px;
            position: relative;
            flex-shrink: 0;
            margin-bottom: 10px;
        }

        .info-section {
            background: transparent;
            border-radius: 16px;
            padding: 0;
            margin-bottom: 0;
            color: #0f172a;
        }

        .jadwalkerja-row {
            background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #115e59 50%, #042f2e 100%);
            border-radius: 20px;
            box-shadow: 0 8px 24px -4px rgba(15, 118, 110, 0.35);
            margin: 0;
            padding: 10px 6px;
            display: flex;
            justify-content: space-between;
            border: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
        }

        .jadwalkerja-col:not(:last-child) {
            border-right: 1px solid rgba(255, 255, 255, 0.18);
        }

        .jadwalkerja-col {
            padding: 2px 6px;
            flex: 1;
        }

        .jadwalkerja-icon {
            font-size: 20px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2px;
        }

        .jadwalkerja-label {
            font-size: 10.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.75);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 2px;
        }

        .jadwalkerja-value {
            font-size: 13px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.02em;
            word-break: break-word;
            line-height: 1.2;
        }

        /* Glassmorphic floating pills over camera */
        .abs-tanggal-modern {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(15, 23, 42, 0.65);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            border-radius: 9999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            z-index: 10;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .abs-jam-modern {
            position: absolute;
            top: 14px;
            right: 14px;
            background: rgba(15, 23, 42, 0.65);
            color: #10b981;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            border-radius: 9999px;
            padding: 4px 10px;
            font-size: 11.5px;
            font-weight: 800;
            z-index: 10;
            letter-spacing: 0.05em;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
            height: 90px; 
            width: 84%;
            margin: 0 auto;
            opacity: 0.55;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
            border: 1.5px solid rgba(255, 255, 255, 0.3);
        }

        #liveness-status-section {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 10px 14px;
            margin-bottom: 10px;
            text-align: center;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
            transition: all 0.25s ease;
        }

        #liveness-status-text {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 700;
            font-size: 12.5px;
            color: #0f766e;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Modern SweetAlert Choice Modal */
        .swal2-popup.swal2-modern-choice-popup {
            border-radius: 24px !important;
            padding: 24px 20px 20px !important;
            max-width: 90% !important;
            width: 360px !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        body.dark .swal2-popup.swal2-modern-choice-popup,
        body.dark-mode-active .swal2-popup.swal2-modern-choice-popup,
        .dark .swal2-popup.swal2-modern-choice-popup {
            background: #0f172a !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8) !important;
        }

        body.dark .swal-choice-title,
        body.dark-mode-active .swal-choice-title,
        .dark .swal-choice-title {
            color: #f8fafc !important;
        }

        body.dark .swal-choice-desc,
        body.dark-mode-active .swal-choice-desc,
        .dark .swal-choice-desc {
            color: #94a3b8 !important;
        }

        .btn-absen-choice {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-absen-choice:active {
            transform: scale(0.97);
        }
        .swal2-modern-cancel-btn {
            border-radius: 12px !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            padding: 8px 18px !important;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <div id="header-section">
        <div class="appHeader bg-primary text-light" style="background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #115e59 100%) !important; border-bottom: 1px solid rgba(255,255,255,0.15); box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <div class="left">
                <a href="{{ route('dashboard.index') }}" class="headerButton goBack" style="color:#ffffff;">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle text-center" style="font-family:'Plus Jakarta Sans',sans-serif;line-height:1.2;">
                <div style="font-weight:800;font-size:15.5px;letter-spacing:-0.02em;color:#ffffff;">SM-Attendance</div>
                <div style="font-size:9.5px;font-weight:600;color:rgba(255,255,255,0.75);letter-spacing:0.04em;">by Zhansoft</div>
            </div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section">
        <div class="presensi-content-modern">
            <div class="camera-section" style="position:relative;">
                <div class="row" style="margin-top: 0;">
                    <div class="col" id="facedetection" style="position:relative;">
                        <div class="abs-tanggal-modern">
                            <span>📅</span>
                            <span>{{ DateToIndo(date('Y-m-d')) }}</span>
                        </div>
                        <div class="abs-jam-modern"><span id="jam"></span></div>
                        <div class="webcam-capture"></div>
                        <input type="hidden" id="server-time" value="{{ date('Y-m-d H:i:s') }}">
                        <input type="hidden" id="lock-location" value="{{ $karyawan->lock_location }}">
                        <div class="map-absolute-section">
                            <div id="map">
                                <div id="map-loading">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <div class="mt-2" style="font-size:10px;">Memuat peta lokasi...</div>
                                </div>
                            </div>
                        </div>
                        @if ($karyawan->lock_location == 0)
                            {{-- lock_location = 0: Hidden input saja --}}
                            <input type="hidden" name="cabang" id="cabang" value="{{ $lokasi_kantor->lokasi_cabang }}">
                        @elseif ($general_setting->multi_lokasi == 0)
                            {{-- lock_location = 1 + multi_lokasi = 0: Hidden input cabang utama --}}
                            <input type="hidden" name="cabang" id="cabang" value="{{ $lokasi_kantor->lokasi_cabang }}">
                        @else
                            {{-- lock_location = 1 + multi_lokasi = 1: Dropdown untuk pilih cabang --}}
                            <div id="listcabang">
                                <div class="select-wrapper">
                                    <select name="cabang" id="cabang" class="form-control" required>
                                        <option value="">-- Pilih Cabang --</option>
                                        @foreach ($cabang as $item)
                                            <option value="{{ $item->lokasi_cabang }}"
                                                {{ $item->kode_cabang == $karyawan->kode_cabang ? 'selected' : '' }}>
                                                {{ $item->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div id="liveness-status-section">
                <span id="liveness-status-text">
                    <i class="fa-solid fa-face-smile"></i>
                    <span>Menunggu pengenalan wajah...</span>
                </span>
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
        
        // Variabel untuk tracking perubahan cabang
        let isWaitingForCabangSelection = false;
        let isCabangChanged = false;

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
            var savedTheme = localStorage.getItem('smatt_theme') || (localStorage.getItem('MobilekitDarkModeActive') === '1' ? 'dark' : null);
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                $('html, body').addClass('dark dark-mode-active');
            }

            Swal.fire({
                title: '<div class="swal-choice-title" style="font-family:\'Plus Jakarta Sans\',sans-serif;font-weight:800;font-size:18px;letter-spacing:-0.02em;">Pilih Tipe Absensi</div>',
                html: `
                    <div style="font-family:'Plus Jakarta Sans',sans-serif;padding:2px 0 6px;">
                        <p class="swal-choice-desc" style="font-size:12.5px;margin-bottom:16px;line-height:1.45;">
                            Silakan tentukan aktivitas kehadiran Anda untuk shift <b>{{ $jam_kerja->nama_jam_kerja }}</b>:
                        </p>
                        <div style="display:flex;flex-direction:column;gap:12px;">
                            <button type="button" id="btn-pilih-masuk" class="btn-absen-choice" style="
                                display:flex;align-items:center;gap:14px;padding:12px 14px;
                                background:linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
                                color:#ffffff;border:none;border-radius:16px;cursor:pointer;
                                text-align:left;box-shadow:0 6px 16px -2px rgba(15,118,110,0.35);
                            ">
                                <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,0.22);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                                    📥
                                </div>
                                <div style="flex:1;">
                                    <div style="font-weight:800;font-size:14px;line-height:1.2;">Absen Masuk</div>
                                    <div style="font-size:11px;opacity:0.9;margin-top:2px;">Mulai jam kerja & catat kehadiran</div>
                                </div>
                                <div style="font-size:16px;opacity:0.75;font-weight:700;">➔</div>
                            </button>

                            <button type="button" id="btn-pilih-pulang" class="btn-absen-choice" style="
                                display:flex;align-items:center;gap:14px;padding:12px 14px;
                                background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                                color:#ffffff;border:none;border-radius:16px;cursor:pointer;
                                text-align:left;box-shadow:0 6px 16px -2px rgba(217,119,6,0.35);
                            ">
                                <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,0.22);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                                    📤
                                </div>
                                <div style="flex:1;">
                                    <div style="font-weight:800;font-size:14px;line-height:1.2;">Absen Pulang</div>
                                    <div style="font-size:11px;opacity:0.9;margin-top:2px;">Selesai jam kerja & catat kepulangan</div>
                                </div>
                                <div style="font-size:16px;opacity:0.75;font-weight:700;">➔</div>
                            </button>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Batal & Kembali',
                cancelButtonColor: '#94a3b8',
                allowOutsideClick: false,
                allowEscapeKey: false,
                customClass: {
                    popup: 'swal2-modern-choice-popup',
                    cancelButton: 'swal2-modern-cancel-btn'
                },
                didOpen: () => {
                    document.getElementById('btn-pilih-masuk')?.addEventListener('click', () => {
                        selectedAbsenType = '1';
                        Swal.close();
                        checkAndProceedAbsen();
                    });
                    document.getElementById('btn-pilih-pulang')?.addEventListener('click', () => {
                        selectedAbsenType = '2';
                        Swal.close();
                        checkAndProceedAbsen();
                    });
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = '/dashboard';
                }
            });

            // Fungsi untuk check approval izin sebelum proceed
            function checkAndProceedAbsen() {
                // Check apakah ada approval izin untuk hari ini
                fetch('/presensi/check-approval-izin?tanggal=' + new Date().toISOString().split('T')[0])
                    .then(response => response.json())
                    .then(data => {
                        if (data.has_approval) {
                            // Ada approval izin, tampilkan warning dan jangan lanjutkan
                            stopAllAudio();
                            speakInstruction(data.message);
                            swal.fire({
                                icon: 'warning',
                                title: 'Tidak Dapat Absen',
                                text: data.message,
                                showConfirmButton: true,
                                confirmButtonText: 'Kembali ke Dashboard',
                            }).then(() => {
                                window.location.href = '/dashboard';
                            });
                        } else {
                            // Tidak ada approval izin, lanjutkan proses absen
                            proceedWithAttendance();
                        }
                    })
                    .catch(error => {
                        console.error('Error checking approval:', error);
                        // Jika error, lanjutkan dengan proses normal (validasi server akan menangani)
                        proceedWithAttendance();
                    });
            }

            function proceedWithAttendance() {
                if (selectedAbsenType === '1') {
                    speakInstruction("Anda memilih Absen Masuk. Mohon tunggu proses verifikasi wajah dan lokasi.");
                } else {
                    speakInstruction("Anda memilih Absen Pulang. Mohon tunggu proses verifikasi wajah dan lokasi.");
                }
                
                initAttendanceProcess();
            }

            function initAttendanceProcess() {
                let multi_lokasi = {{ $general_setting->multi_lokasi }};
                let lokasi_cabang = multi_lokasi ? document.getElementById('cabang').value : "{{ $lokasi_kantor->lokasi_cabang }}";
                let is_niqab = {{ $karyawan->is_niqab ?? 0 }};
                let isCabangSelected = !multi_lokasi; // Jika single lokasi, langsung true
                
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
                                    if (multi_lokasi == 1) {
                                        if (isCabangChanged) {
                                            // User baru saja mengubah cabang, langsung submit setelah peta dimuat
                                            isAttendanceSubmitted = true;
                                            isCabangChanged = false;
                                            stopAllAudio();
                                            speakInstruction("Cabang dipilih. Memproses absensi Anda.");
                                            setTimeout(() => {
                                                handleAbsen(selectedAbsenType);
                                            }, 1500);
                                        } else if (!isWaitingForCabangSelection) {
                                            // Pertama kali peta dimuat, tunggu user memilih cabang
                                            isWaitingForCabangSelection = true;
                                            speakInstruction("Silakan pilih cabang lokasi absensi Anda.");
                                            // Set timeout untuk auto-submit jika user tidak memilih dalam 15 detik
                                            setTimeout(() => {
                                                if (!isAttendanceSubmitted) {
                                                    isAttendanceSubmitted = true;
                                                    stopAllAudio();
                                                    speakInstruction("Memproses absensi Anda dengan pengaturan cabang saat ini.");
                                                    setTimeout(() => {
                                                        handleAbsen(selectedAbsenType);
                                                    }, 1500);
                                                }
                                            }, 15000);
                                        }
                                    } else {
                                        // Jika single lokasi, submit langsung dengan jeda
                                        setTimeout(() => {
                                            if (!isAttendanceSubmitted) {
                                                isAttendanceSubmitted = true;
                                                stopAllAudio();
                                                speakInstruction("Verifikasi berhasil. Memproses absensi Anda.");
                                                setTimeout(() => {
                                                    handleAbsen(selectedAbsenType);
                                                }, 1500);
                                            }
                                        }, 3000);
                                    }
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
                                if (multi_lokasi == 1) {
                                    if (isCabangChanged) {
                                        // User baru saja mengubah cabang, langsung submit setelah peta dimuat
                                        isAttendanceSubmitted = true;
                                        isCabangChanged = false;
                                        stopAllAudio();
                                        speakInstruction("Cabang dipilih. Memproses absensi Anda.");
                                        setTimeout(() => {
                                            handleAbsen(selectedAbsenType);
                                        }, 1500);
                                    } else if (!isWaitingForCabangSelection) {
                                        // Pertama kali peta dimuat, tunggu user memilih cabang
                                        isWaitingForCabangSelection = true;
                                        speakInstruction("Silakan pilih cabang lokasi absensi Anda.");
                                        // Set timeout untuk auto-submit jika user tidak memilih dalam 15 detik
                                        setTimeout(() => {
                                            if (!isAttendanceSubmitted) {
                                                isAttendanceSubmitted = true;
                                                stopAllAudio();
                                                speakInstruction("Memproses absensi Anda dengan pengaturan cabang saat ini.");
                                                setTimeout(() => {
                                                    handleAbsen(selectedAbsenType);
                                                }, 1500);
                                            }
                                        }, 15000);
                                    }
                                } else {
                                    // Jika single lokasi, submit langsung dengan jeda
                                    setTimeout(() => {
                                        if (!isAttendanceSubmitted) {
                                            isAttendanceSubmitted = true;
                                            stopAllAudio();
                                            speakInstruction("Verifikasi berhasil. Memproses absensi Anda.");
                                            setTimeout(() => {
                                                handleAbsen(selectedAbsenType);
                                            }, 1500);
                                        }
                                    }, 3000);
                                }
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

                                            // Update liveness status element below canvas
                                            const livenessStatusEl = document.getElementById('liveness-status-text');
                                            const livenessStatusSection = document.getElementById('liveness-status-section');
                                            if (livenessStatusEl && livenessStatusSection) {
                                                if (faceRecognitionDetected == 1) {
                                                    if (livenessPassed) {
                                                        livenessStatusEl.textContent = "✓ Liveness Terverifikasi!";
                                                        livenessStatusEl.style.color = "#28a745";
                                                        livenessStatusSection.style.background = "linear-gradient(135deg, #d4edda 0%, #fff 100%)";
                                                    } else {
                                                        livenessStatus = `Silakan ${livenessInstructions[livenessInstructionIndex]}`;
                                                        livenessStatusEl.textContent = `${livenessStatus} (${livenessInstructionIndex + 1}/${livenessInstructions.length})`;
                                                        livenessStatusEl.style.color = "#0c5460";
                                                        livenessStatusSection.style.background = "linear-gradient(135deg, #d1ecf1 0%, #fff 100%)";
                                                    }
                                                } else {
                                                    livenessStatusEl.textContent = "Menunggu pengenalan wajah...";
                                                    livenessStatusEl.style.color = "#0c5460";
                                                    livenessStatusSection.style.background = "linear-gradient(135deg, #d1ecf1 0%, #fff 100%)";
                                                }
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
                    // Hide liveness status section when face recognition is disabled
                    const livenessStatusSection = document.getElementById('liveness-status-section');
                    if (livenessStatusSection) {
                        livenessStatusSection.style.display = 'none';
                    }
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
                    const lockLocation = parseInt(document.getElementById('lock-location').value);
                    const [officeLat, officeLon] = lokasi_cabang.split(',').map(parseFloat);
                    const officeRadius = parseFloat("{{ $lokasi_kantor->radius_cabang }}");

                    lokasi = `${userLatitude},${userLongitude},${userAccuracy}`;

                    // Jika lock_location = 0, skip validasi radius dan accuracy
                    if (lockLocation == 0) {
                        // Karyawan bisa absen dimana saja, langsung proses absen
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
                        return;
                    }

                    // Jika lock_location = 1, lakukan validasi radius dan accuracy
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

                    // Set flag untuk tracking bahwa user mengubah cabang
                    isCabangChanged = true;

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