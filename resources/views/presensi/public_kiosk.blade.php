@extends('layouts.kiosk')

@push('mystyle')
<style>
    /* ============================================
       KIOSK PRESENSI — Semi-Formal Modern
       ============================================ */

    .kiosk-page {
        display: flex;
        height: 100vh;
        overflow: hidden;
    }

    /* ---- Left Panel (Branding + Camera) ---- */
    .kiosk-left {
        flex: 0 0 55%;
        background: linear-gradient(160deg, color-mix(in srgb, var(--primary) 90%, #000 10%), var(--primary));
        color: #fff;
        display: flex;
        flex-direction: column;
        padding: 3rem 3.5rem;
        position: relative;
        overflow: hidden;
    }

    .kiosk-left::after {
        content: '';
        position: absolute;
        width: 600px;
        height: 600px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        bottom: -200px;
        right: -150px;
        pointer-events: none;
    }

    .brand-area {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2.5rem;
    }

    .brand-logo {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(8px);
    }

    .brand-logo img {
        width: 32px;
        height: 32px;
        object-fit: contain;
    }

    .brand-text h2 {
        font-size: 1.35rem;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .brand-text span {
        font-size: 0.8rem;
        opacity: 0.7;
        font-weight: 400;
    }

    .camera-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 1.5rem;
    }

    .camera-frame {
        position: relative;
        border-radius: 1.25rem;
        overflow: hidden;
        background: #000;
        aspect-ratio: 16/10;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        border: 3px solid rgba(255,255,255,0.15);
    }

    .camera-frame .webcam-capture,
    .camera-frame .webcam-capture video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
        display: block;
    }

    .camera-label {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        background: rgba(0,0,0,0.55);
        backdrop-filter: blur(8px);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.4rem 0.85rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        letter-spacing: 0.03em;
    }

    .camera-label .dot {
        width: 7px;
        height: 7px;
        background: #ef4444;
        border-radius: 50%;
        animation: blink 1.5s infinite;
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    .camera-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        opacity: 0.6;
    }

    .loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 20;
    }

    .loading-overlay.active { display: flex; }

    .spinner {
        width: 44px;
        height: 44px;
        border: 4px solid rgba(255,255,255,0.2);
        border-left-color: #fff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* ---- Right Panel (Clock + Status) ---- */
    .kiosk-right {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 3rem 3.5rem;
        background: #f8f9fb;
        overflow-y: auto;
    }

    .clock-section {
        text-align: center;
        padding: 2.5rem 0 2rem;
    }

    .clock-time {
        font-family: 'JetBrains Mono', monospace;
        font-size: 4.5rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.04em;
        line-height: 1;
    }

    .clock-date {
        font-size: 1rem;
        color: #64748b;
        margin-top: 0.6rem;
        font-weight: 500;
    }

    .divider {
        height: 1px;
        background: #e2e8f0;
        margin: 1.5rem 0;
    }

    /* ---- Instruction Card ---- */
    .instruction-card {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 1.25rem;
        padding: 2rem;
        animation: fadeIn 0.5s ease;
    }

    .rfid-icon-wrapper {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(var(--primary-rgb), 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulseRing 2.5s infinite ease-in-out;
    }

    .rfid-icon-wrapper ion-icon {
        font-size: 3rem;
        color: var(--primary);
    }

    @keyframes pulseRing {
        0% { box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0.3); }
        70% { box-shadow: 0 0 0 20px rgba(var(--primary-rgb), 0); }
        100% { box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0); }
    }

    .instruction-card h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
    }

    .instruction-card p {
        color: #64748b;
        font-size: 0.95rem;
        max-width: 320px;
        line-height: 1.6;
    }

    /* ---- Employee Card ---- */
    .employee-card {
        display: none;
        background: #fff;
        border-radius: 1.25rem;
        padding: 2rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .employee-card.error {
        border-color: #fca5a5;
        box-shadow: 0 4px 20px rgba(239, 68, 68, 0.1);
    }

    .employee-card .emp-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }

    .emp-avatar {
        width: 64px;
        height: 64px;
        border-radius: 1rem;
        background: rgba(var(--primary-rgb), 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.75rem;
        flex-shrink: 0;
    }

    .emp-meta h2 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.2rem;
    }

    .emp-meta p {
        font-size: 0.85rem;
        color: #64748b;
        font-family: 'JetBrains Mono', monospace;
    }

    .emp-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 1rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        margin-top: 0.75rem;
    }

    .emp-badge.masuk {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .emp-badge.pulang {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .emp-badge.error {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .emp-detail-rows {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid #e2e8f0;
    }

    .emp-detail-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .emp-detail-row ion-icon {
        font-size: 1.15rem;
        color: var(--primary);
        flex-shrink: 0;
    }

    .emp-detail-row .detail-label {
        display: block;
        font-size: 0.7rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .emp-detail-row .detail-value {
        display: block;
        font-size: 0.9rem;
        color: #1e293b;
        font-weight: 600;
    }

    .emp-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 1rem;
    }

    /* ---- Bottom Info ---- */
    .kiosk-footer {
        margin-top: auto;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .kiosk-footer .status-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        margin-right: 0.4rem;
        vertical-align: middle;
    }

    /* ---- Toast Notification ---- */
    .toast-notif {
        position: fixed;
        top: 2rem;
        right: 2rem;
        min-width: 360px;
        padding: 1.25rem 1.5rem;
        border-radius: 1rem;
        display: none;
        z-index: 999;
        animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    }

    .toast-notif.success {
        background: #fff;
        border-left: 5px solid #10b981;
        color: #064e3b;
    }

    .toast-notif.error {
        background: #fff;
        border-left: 5px solid #ef4444;
        color: #7f1d1d;
    }

    .toast-notif .toast-body {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .toast-notif .toast-body ion-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .toast-notif .toast-body .toast-text strong {
        display: block;
        font-size: 0.95rem;
    }

    .toast-notif .toast-body .toast-text span {
        font-size: 0.82rem;
        color: #64748b;
    }

    /* ---- Hidden Input ---- */
    #rfid-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    /* ---- Face Detection Indicator ---- */
    .face-status {
        position: absolute;
        top: 1rem;
        right: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(8px);
        padding: 0.4rem 0.85rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #fff;
        z-index: 10;
        transition: all 0.3s;
        letter-spacing: 0.03em;
    }

    .face-status .face-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #ef4444;
        transition: background 0.3s;
    }

    .face-status.detected .face-dot {
        background: #22c55e;
        box-shadow: 0 0 6px #22c55e;
    }

    .camera-frame.face-ok {
        border-color: #22c55e;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 0 2px rgba(34, 197, 94, 0.3);
    }

    /* ---- Animations ---- */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(40px); }
        to { opacity: 1; transform: translateX(0); }
    }
</style>
@endpush

@section('content')
<input type="text" id="rfid-input" autofocus>

<div class="kiosk-page">
    {{-- ====== LEFT PANEL ====== --}}
    <div class="kiosk-left">
        <div class="brand-area">
            <div class="brand-logo">
                @if (!empty($generalsetting->logo) && Storage::disk('public')->exists('logo/' . $generalsetting->logo))
                    <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo">
                @else
                    <ion-icon name="business-outline" style="font-size: 1.75rem; color: #fff;"></ion-icon>
                @endif
            </div>
            <div class="brand-text">
                <h2>{{ $generalsetting->nama_perusahaan ?? 'Perusahaan' }}</h2>
                <span>Sistem Presensi Digital</span>
            </div>
        </div>

        <div class="camera-section">
            <div class="camera-frame" id="camera-frame">
                <div class="webcam-capture" id="webcam-capture"></div>
                <canvas id="face-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:5;"></canvas>
                <div class="face-status" id="face-status">
                    <span class="face-dot"></span>
                    <span id="face-label">NO FACE</span>
                </div>
                <div class="camera-label">
                    <span class="dot"></span>
                    LIVE CAMERA
                </div>
                <div class="loading-overlay" id="loading-overlay">
                    <div class="spinner"></div>
                </div>
            </div>
            <div class="camera-footer">
                <span>Kamera aktif untuk verifikasi wajah</span>
                <span>Resolusi: 640×480</span>
            </div>
        </div>
    </div>

    {{-- ====== RIGHT PANEL ====== --}}
    <div class="kiosk-right">
        <div class="clock-section">
            <div class="clock-time" id="clock-time">00:00:00</div>
            <div class="clock-date" id="clock-date">Memuat tanggal...</div>
        </div>

        <div class="divider"></div>

        {{-- Instruction --}}
        <div class="instruction-card" id="instruction-card">
            <div class="rfid-icon-wrapper">
                <ion-icon name="scan-outline"></ion-icon>
            </div>
            <h3>Tap Kartu RFID</h3>
            <p>Tempelkan kartu identitas Anda pada alat reader untuk melakukan presensi secara otomatis</p>
        </div>

        {{-- Employee Info (hidden by default) --}}
        <div class="employee-card" id="employee-card">
            <div class="emp-header">
                <div class="emp-avatar" id="emp-avatar">
                    <ion-icon name="person"></ion-icon>
                </div>
                <div class="emp-meta">
                    <h2 id="emp-name">-</h2>
                    <p id="emp-nik" style="font-family: 'JetBrains Mono', monospace; color: #64748b; font-size: 0.85rem;">-</p>
                </div>
            </div>
            <div class="emp-detail-rows">
                <div class="emp-detail-row">
                    <ion-icon name="briefcase-outline"></ion-icon>
                    <div>
                        <span class="detail-label">Jabatan</span>
                        <span class="detail-value" id="emp-jabatan">-</span>
                    </div>
                </div>
                <div class="emp-detail-row">
                    <ion-icon name="people-outline"></ion-icon>
                    <div>
                        <span class="detail-label">Departemen</span>
                        <span class="detail-value" id="emp-dept">-</span>
                    </div>
                </div>
                <div class="emp-detail-row">
                    <ion-icon name="time-outline"></ion-icon>
                    <div>
                        <span class="detail-label">Jadwal Kerja</span>
                        <span class="detail-value" id="emp-jadwal">-</span>
                    </div>
                </div>
            </div>
            <div id="emp-badge-wrapper" style="margin-top: 1rem;">
                <span class="emp-badge masuk" id="emp-status">
                    <ion-icon name="log-in-outline"></ion-icon>
                    ABSEN MASUK
                </span>
            </div>
        </div>

        <div class="kiosk-footer">
            <span><span class="status-dot"></span> Sistem Online</span>
            <span>v2.0</span>
        </div>
    </div>
</div>

{{-- Toast --}}
<div class="toast-notif success" id="toast-success">
    <div class="toast-body">
        <ion-icon name="checkmark-circle" style="color: #10b981;"></ion-icon>
        <div class="toast-text">
            <strong>Presensi Berhasil</strong>
            <span id="toast-msg">Kehadiran sudah dicatat</span>
        </div>
    </div>
</div>

<div class="toast-notif error" id="toast-error">
    <div class="toast-body">
        <ion-icon name="close-circle" style="color: #ef4444;"></ion-icon>
        <div class="toast-text">
            <strong>Gagal</strong>
            <span id="toast-err-msg">Terjadi kesalahan</span>
        </div>
    </div>
</div>

<audio id="audio-success">
    <source src="{{ asset('assets/sound/absenmasuk.wav') }}" type="audio/wav">
</audio>
<audio id="audio-error">
    <source src="{{ asset('assets/sound/radius.mp3') }}" type="audio/mpeg">
</audio>
@endsection

@push('myscript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="{{ asset('assets/vendor/face-api.min.js') }}"></script>
<script>
(function() {
    'use strict';

    // ---- Clock ----
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('clock-time').textContent = `${h}:${m}:${s}`;

        const opts = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        document.getElementById('clock-date').textContent = now.toLocaleDateString('id-ID', opts);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ---- Webcam ----
    Webcam.set({ width: 640, height: 480, image_format: 'jpeg', jpeg_quality: 90 });
    Webcam.attach('.webcam-capture');

    // ---- Face Detection ----
    let faceDetected = false;
    const MODEL_URL = '{{ asset("models") }}';

    async function initFaceDetection() {
        try {
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            console.log('Face detection model loaded');
            startFaceDetection();
        } catch (err) {
            console.warn('Face detection model failed to load:', err);
            // Fallback: allow without face detection
            faceDetected = true;
            $('#face-status').addClass('detected');
            $('#face-label').text('N/A');
        }
    }

    function startFaceDetection() {
        const video = document.querySelector('.webcam-capture video');
        if (!video) {
            setTimeout(startFaceDetection, 500);
            return;
        }

        const canvas = document.getElementById('face-canvas');

        setInterval(async () => {
            if (!video || video.readyState < 2) return;

            const detections = await faceapi.detectAllFaces(
                video,
                new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })
            );

            // Update indicator
            if (detections.length > 0) {
                faceDetected = true;
                $('#face-status').addClass('detected');
                $('#face-label').text('WAJAH TERDETEKSI');
                $('#camera-frame').addClass('face-ok');
            } else {
                faceDetected = false;
                $('#face-status').removeClass('detected');
                $('#face-label').text('NO FACE');
                $('#camera-frame').removeClass('face-ok');
            }

            // Draw bounding box
            const displaySize = { width: video.videoWidth, height: video.videoHeight };
            faceapi.matchDimensions(canvas, displaySize);
            const resized = faceapi.resizeResults(detections, displaySize);
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            resized.forEach(det => {
                const box = det.box;
                ctx.strokeStyle = '#22c55e';
                ctx.lineWidth = 2;
                ctx.strokeRect(box.x, box.y, box.width, box.height);
            });
        }, 500); // Check every 500ms
    }

    // Start face detection after webcam is ready
    setTimeout(initFaceDetection, 2000);

    // ---- Audio ----
    const audioSuccess = document.getElementById('audio-success');
    const audioError   = document.getElementById('audio-error');

    // ---- RFID Input ----
    const rfidInput = document.getElementById('rfid-input');
    document.addEventListener('click', () => rfidInput.focus());
    setInterval(() => { if (document.activeElement !== rfidInput) rfidInput.focus(); }, 1500);

    let isProcessing = false;

    // Global keydown listener — catches RFID scanner output
    let rfidBuffer = '';
    let rfidTimeout = null;

    window.addEventListener('keydown', function(e) {
        if (isProcessing) return;

        if (e.key === 'Enter') {
            clearTimeout(rfidTimeout);
            if (rfidBuffer.length > 3) {
                processRfid(rfidBuffer);
            }
            rfidBuffer = '';
            rfidInput.value = '';
            return;
        }

        if (e.key.length === 1 && /[a-zA-Z0-9]/.test(e.key)) {
            rfidBuffer += e.key;
            clearTimeout(rfidTimeout);
            rfidTimeout = setTimeout(() => { rfidBuffer = ''; }, 300);
        }
    });

    // ---- Process RFID ----
    function processRfid(uid) {
        if (isProcessing) return;
        isProcessing = true;

        showLoadingOverlay(true);
        $('#instruction-card').fadeOut(200, function() {
            $('#employee-card').fadeIn(300);
        });

        $.ajax({
            type: 'POST',
            url: '{{ route("public.presensi.check-rfid") }}',
            data: { _token: '{{ csrf_token() }}', rfid_uid: uid },
            success: function(res) {
                if (res.status === 'success') {
                    $('#emp-name').text(res.nama);
                    $('#emp-nik').text(res.nik);
                    $('#emp-jabatan').text(res.jabatan);
                    $('#emp-dept').text(res.departemen);
                    $('#emp-jadwal').text(res.jam_kerja);

                    // Set foto
                    if (res.foto) {
                        $('#emp-avatar').html('<img src="' + res.foto + '" alt="Foto">');
                    } else {
                        $('#emp-avatar').html('<ion-icon name="person"></ion-icon>');
                    }

                    const badge = $('#emp-status');
                    if (res.type === 'in') {
                        badge.removeClass('pulang').addClass('masuk')
                             .html('<ion-icon name="log-in-outline"></ion-icon> ABSEN MASUK');
                    } else {
                        badge.removeClass('masuk').addClass('pulang')
                             .html('<ion-icon name="log-out-outline"></ion-icon> ABSEN PULANG');
                    }

                    waitForFaceAndSnap(uid);
                } else {
                    showError(res.message);
                }
            },
            error: function() {
                showError('Gagal terhubung ke server');
            }
        });
    }

    // ---- Wait for Face then Snapshot ----
    function waitForFaceAndSnap(uid) {
        if (faceDetected) {
            // Face already detected, snap immediately
            showLoadingOverlay(true);
            takeSnapshot(uid);
            return;
        }

        // Show message asking to face the camera
        showToast('error', 'Silakan arahkan wajah Anda ke kamera...');

        let attempts = 0;
        const maxAttempts = 16; // 16 x 500ms = 8 seconds max
        const faceCheck = setInterval(() => {
            attempts++;
            if (faceDetected) {
                clearInterval(faceCheck);
                $('#toast-error').fadeOut(200);
                showLoadingOverlay(true);
                takeSnapshot(uid);
            } else if (attempts >= maxAttempts) {
                clearInterval(faceCheck);
                showError('Wajah tidak terdeteksi. Silakan arahkan wajah ke kamera dan coba lagi.');
            }
        }, 500);
    }

    // ---- Snapshot & Store ----
    function takeSnapshot(uid) {
        Webcam.snap(function(dataUri) {
            $.ajax({
                type: 'POST',
                url: '{{ route("public.presensi.store") }}',
                data: { _token: '{{ csrf_token() }}', rfid_uid: uid, image: dataUri },
                success: function(res) {
                    showLoadingOverlay(false);
                    if (res.status === 'success') {
                        audioSuccess.currentTime = 0;
                        audioSuccess.play();
                        showToast('success', res.message);
                        setTimeout(resetKiosk, 4000);
                    } else {
                        showError(res.message);
                    }
                },
                error: function() {
                    showError('Gagal menyimpan data presensi');
                }
            });
        });
    }

    // ---- Error Handler ----
    function showError(msg) {
        showLoadingOverlay(false);
        audioError.currentTime = 0;
        audioError.play();

        // If employee card is visible, turn it red
        if ($('#employee-card').is(':visible')) {
            $('#employee-card').addClass('error');
            const badge = $('#emp-status');
            badge.removeClass('masuk pulang').addClass('error')
                 .html('<ion-icon name="close-circle-outline"></ion-icon> GAGAL');
        }

        showToast('error', msg);
        setTimeout(resetKiosk, 3000);
    }

    // ---- Toast ----
    function showToast(type, msg) {
        if (type === 'success') {
            $('#toast-msg').text(msg);
            $('#toast-success').fadeIn(300);
            setTimeout(() => $('#toast-success').fadeOut(300), 4000);
        } else {
            $('#toast-err-msg').text(msg);
            $('#toast-error').fadeIn(300);
            setTimeout(() => $('#toast-error').fadeOut(300), 3000);
        }
    }

    // ---- Reset ----
    function resetKiosk() {
        showLoadingOverlay(false);
        $('#employee-card').removeClass('error').fadeOut(200, function() {
            $('#instruction-card').fadeIn(300);
            $('#emp-name').text('-');
            $('#emp-nik').text('-');
            $('#emp-jabatan').text('-');
            $('#emp-dept').text('-');
            $('#emp-jadwal').text('-');
            $('#emp-avatar').html('<ion-icon name="person"></ion-icon>');
            $('#emp-status').removeClass('error pulang').addClass('masuk')
                .html('<ion-icon name="log-in-outline"></ion-icon> ABSEN MASUK');
        });
        rfidBuffer = '';
        rfidInput.value = '';
        isProcessing = false;
    }

    // ---- Loading Overlay ----
    function showLoadingOverlay(show) {
        if (show) {
            $('#loading-overlay').addClass('active');
        } else {
            $('#loading-overlay').removeClass('active');
        }
    }
})();
</script>
@endpush
