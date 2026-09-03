@extends('layouts.mobile.app')
@section('content')
    <style>
        /* Modern Fullscreen Layout */
        body {
            background-color: #000;
            overflow: hidden;
        }

        .camera-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
        }

        #webcam-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1); /* Mirror effect */
        }

        /* UI Overlays */
        .overlay-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 20px 100px;
        }

        /* Face Frame Guide */
        .face-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -60%);
            width: 280px;
            height: 380px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 180px; /* Oval shape for face */
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.7); /* Darken outside */
            transition: all 0.3s ease;
        }

        .face-frame.active {
            border-color: #22c55e; /* Green */
            border-width: 4px;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.85), 0 0 50px rgba(34, 197, 94, 0.5);
        }

        .face-frame.scanning::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 180px;
            box-shadow: inset 0 0 20px #22c55e;
            animation: pulse-green 1.5s infinite;
        }

        @keyframes pulse-green {
            0% { opacity: 0.3; }
            50% { opacity: 0.7; }
            100% { opacity: 0.3; }
        }

        /* Status & Instructions */
        .status-badge {
            align-self: center;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 30px;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            margin-top: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            display: inline-block;
        }
        .status-dot.ready { background: #22c55e; box-shadow: 0 0 10px #22c55e; }

        /* Scanning Progress */
        .scan-progress-container {
            position: absolute;
            bottom: -60px;
            left: 50%;
            transform: translateX(-50%);
            width: 200px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .scan-progress-container.show { opacity: 1; bottom: 20px; }

        .progress-bar-wrapper {
            background: rgba(255,255,255,0.2);
            height: 6px;
            border-radius: 3px;
            width: 100%;
            overflow: hidden;
            margin-top: 8px;
        }
        .progress-bar-fill {
            background: #22c55e;
            height: 100%;
            width: 0%;
            transition: width 0.2s linear;
        }
        .scan-text {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Action Buttons */
        .action-area {
            position: absolute;
            bottom: 120px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            pointer-events: auto;
        }

        .btn-modern-start {
            background: #fff;
            color: #000;
            border: none;
            padding: 18px 40px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.2s;
        }
        .btn-modern-start:active { transform: scale(0.95); }
        .btn-modern-start i { font-size: 20px; }

        .loading-spinner {
            display: none;
            width: 24px;
            height: 24px;
            border: 3px solid rgba(0,0,0,0.1);
            border-top-color: #000;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Success State */
        .success-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            z-index: 20;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            pointer-events: auto;
        }
        .success-icon {
            font-size: 80px;
            color: #22c55e;
            margin-bottom: 20px;
            animation: pop-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes pop-in { 
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Warning Toast */
        .warning-toast {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100px);
            background: rgba(239, 68, 68, 0.9);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 100;
        }
        .warning-toast.show { transform: translateX(-50%) translateY(0); }

    </style>

    <!-- Camera View -->
    <div class="camera-container">
        <video id="webcam-video" autoplay playsinline muted></video>
    </div>

    <!-- Interface Overlays -->
    <div class="overlay-container">
        <!-- Top Status -->
        <div class="status-badge">
            <span class="status-dot" id="statusDot"></span>
            <span id="statusText">Menunggu kamera...</span>
        </div>

        <!-- Warning Toast -->
        <div class="warning-toast" id="warningToast">
            <i class="ti ti-alert-circle"></i>
            <span id="warningMessage">Peringatan</span>
        </div>

        <!-- Center Face Frame -->
        <div class="face-frame" id="faceFrame">
            <!-- Progress shown below frame -->
            <div class="scan-progress-container" id="scanProgress">
                <div class="scan-text">Merekam Wajah...</div>
                <div class="progress-bar-wrapper">
                    <div class="progress-bar-fill" id="progressBarFill"></div>
                </div>
                <div style="font-size: 12px; color: rgba(255,255,255,0.7); margin-top: 5px;">Tahan posisi... <span id="progressPercent">0%</span></div>
            </div>
        </div>

        <!-- Bottom Actions -->
        <div class="action-area" id="actionArea">
            <button class="btn-modern-start" id="btnStart" onclick="startScanning()">
                <div class="loading-spinner" id="btnSpinner"></div>
                <i class="ti ti-face-id" id="btnIcon"></i>
                <span id="btnText">Mulai Scan Wajah</span>
            </button>
        </div>
    </div>

    <!-- Success Screen -->
    <div class="success-overlay" id="successScreen">
        <i class="ti ti-circle-check-filled success-icon"></i>
        <h2 class="mb-2">Berhasil!</h2>
        <p class="text-white-50 text-center px-4">Data wajah berhasil didaftarkan.<br>Mengalihkan anda kembali...</p>
    </div>

    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // Configuration
        const TOTAL_IMAGES_NEEDED = 5;
        const CAPTURE_INTERVAL = 300; // ms between captures
        const CONFIDENCE_THRESHOLD = 0.5;
        
        // State
        let modelLoaded = false;
        let isScanning = false;
        let imagesCaptured = [];
        let videoEl = document.getElementById('webcam-video');
        let stream = null;

        // UI Elements
        const statusText = document.getElementById('statusText');
        const statusDot = document.getElementById('statusDot');
        const faceFrame = document.getElementById('faceFrame');
        const warningToast = document.getElementById('warningToast');
        const btnStart = document.getElementById('btnStart');
        const actionArea = document.getElementById('actionArea');
        const scanProgress = document.getElementById('scanProgress');
        const progressBarFill = document.getElementById('progressBarFill');
        const progressPercent = document.getElementById('progressPercent');

        // Initialize
        document.addEventListener('DOMContentLoaded', async () => {
            await startCamera();
            await loadModels();
        });

        // 1. Start Camera
        async function startCamera() {
            try {
                // Constraints for optimal face/portrait mode
                const constraints = {
                    video: {
                        facingMode: 'user',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                };
                
                stream = await navigator.mediaDevices.getUserMedia(constraints);
                videoEl.srcObject = stream;
                
                // Wait for video to play to confirm dimensions
                await new Promise(resolve => videoEl.onloadedmetadata = resolve);
                videoEl.play();
                
                updateStatus('ready', 'Kamera Siap. Klik Mulai.');
                btnStart.disabled = false;
                
            } catch (err) {
                console.error("Camera Error:", err);
                updateStatus('error', 'Gagal akses kamera. Izinkan akses.');
                showWarning('Gagal mengakses kamera.', true);
            }
        }

        // 2. Load Face API Models
        async function loadModels() {
            updateStatus('loading', 'Memuat model AI...');
            btnStart.disabled = true;
            
            try {
                await faceapi.nets.tinyFaceDetector.loadFromUri('{{ asset("models") }}');
                // Optional: load landmarks if we want strict checks, but tinyDetector is enough for simple presence
                // await faceapi.nets.faceLandmark68Net.loadFromUri('/models'); 
                
                modelLoaded = true;
                updateStatus('ready', 'Kamera Siap. Klik tombol Mulai.');
                btnStart.disabled = false;
                console.log("Models loaded");
            } catch (err) {
                console.error("Model Load Error:", err);
                updateStatus('error', 'Gagal memuat model AI.');
            }
        }

        // 3. User Clicks Start
        async function startScanning() {
            if (!modelLoaded || !stream) return;
            
            isScanning = true;
            imagesCaptured = []; // Reset
            
            // UI Updates
            btnStart.style.display = 'none'; // Hide button to declutter
            faceFrame.classList.add('scanning');
            scanProgress.classList.add('show');
            updateProgress(0);
            
            updateStatus('loading', 'Mencari wajah...');
            
            // Start Detection Loop
            detectLoop();
        }

        // 4. Detection Loop
        async function detectLoop() {
            if (!isScanning) return;

            // Detect face using TinyFaceDetector (fastest)
            // We use inputSize 224 or 320 for speed on mobile
            const detection = await faceapi.detectSingleFace(videoEl, new faceapi.TinyFaceDetectorOptions({ inputSize: 320 }));

            if (detection && detection.score > CONFIDENCE_THRESHOLD) {
                // Face detected!
                const box = detection.box;
                
                // Simple centering check (optional, but good UX)
                // We define a "safe zone" in the center
                const videoWidth = videoEl.videoWidth;
                const videoHeight = videoEl.videoHeight;
                const centerX = box.x + (box.width / 2);
                const centerY = box.y + (box.height / 2);
                
                // Check if face is roughly centered (within middle 60%)
                const isCentered = (centerX > videoWidth * 0.2 && centerX < videoWidth * 0.8) &&
                                   (centerY > videoHeight * 0.2 && centerY < videoHeight * 0.8);
                
                // Check if face is big enough
                const isCloseEnough = box.width > videoWidth * 0.15; // Face width > 15% of screen

                if (isCentered && isCloseEnough) {
                    faceFrame.classList.add('active'); // Green border
                    updateStatus('success', 'Wajah terdeteksi. Tahan...');
                    hideWarning();
                    
                    // Capture Frame!
                    await captureFrame();
                    
                } else {
                    faceFrame.classList.remove('active');
                    if (!isCloseEnough) {
                        updateStatus('warning', 'Mendekat ke kamera');
                    } else {
                        updateStatus('warning', 'Posisikan wajah di tengah');
                    }
                }
            } else {
                faceFrame.classList.remove('active');
                updateStatus('loading', 'Wajah tidak terdeteksi...');
            }

            // Continue loop if not done
            if (imagesCaptured.length < TOTAL_IMAGES_NEEDED) {
                requestAnimationFrame(detectLoop);
            } else {
                finishScanning();
            }
        }

        // 5. Capture Frame Logic
        let lastCaptureTime = 0;
        async function captureFrame() {
            const now = Date.now();
            if (now - lastCaptureTime < CAPTURE_INTERVAL) return; // Debounce
            
            lastCaptureTime = now;
            
            // Draw video frame to canvas
            const canvas = document.createElement('canvas');
            canvas.width = videoEl.videoWidth;
            canvas.height = videoEl.videoHeight;
            const ctx = canvas.getContext('2d');
            
            // Mirror flip if using front camera usually mirrors, but we want the raw image?
            // Actually, for recognition, standard orientation is best. 
            // The video preview is css mirrored. We draw raw.
            ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);
            
            // Convert to base64
            const dataUrl = canvas.toDataURL('image/jpeg', 0.8); // 80% quality
            
            imagesCaptured.push(dataUrl);
            console.log(`Captured ${imagesCaptured.length}/${TOTAL_IMAGES_NEEDED}`);
            
            // Update Progress UI
            const pct = Math.round((imagesCaptured.length / TOTAL_IMAGES_NEEDED) * 100);
            updateProgress(pct);
        }

        function updateProgress(percent) {
            progressBarFill.style.width = percent + '%';
            progressPercent.innerText = percent + '%';
        }

        // 6. Finish & Upload
        async function finishScanning() {
            isScanning = false;
            faceFrame.classList.remove('scanning');
            faceFrame.classList.add('active'); // Stay green
            
            updateStatus('success', 'Perekaman Selesai! Mengunggah...');
            scanProgress.classList.remove('show');
            
            // Show Spinner on Start Button (if we wanted to reuse it, but we hid it)
            // Let's create an upload form data
            
            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('nik', '{{ $nik }}');
                
                // Convert captured base64 images to Blobs and append
                for (let i = 0; i < imagesCaptured.length; i++) {
                    const blob = await (await fetch(imagesCaptured[i])).blob();
                    formData.append('files[]', blob, `capture_${i+1}.jpg`);
                }
                
                // Add dummy metadata to satisfy backend requirement structure
                // Backend expects metadata json with direction keys
                const metadata = imagesCaptured.map(() => ({ direction: 'front' }));
                formData.append('metadata', JSON.stringify(metadata));

                // Send AJAX
                updateStatus('loading', 'Mengeirim data ke server...');
                
                $.ajax({
                    type: 'POST',
                    url: '{{ route("facerecognition.store") }}',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showSuccessScreen();
                        } else {
                            handleUploadError(response.message);
                        }
                    },
                    error: function(err) {
                        console.error("Upload Error", err);
                        handleUploadError('Terjadi kesalahan koneksi.');
                    }
                });

            } catch (err) {
                console.error("Processing Error", err);
                handleUploadError('Gagal memproses gambar.');
            }
        }

        function showSuccessScreen() {
            const successScreen = document.getElementById('successScreen');
            successScreen.style.display = 'flex';
            setTimeout(() => {
                window.location.href = "{{ route('dashboard.index') }}";
            }, 2000);
        }

        function handleUploadError(msg) {
            alert("Error: " + msg);
            // Reset to allow retry
            isScanning = false;
            imagesCaptured = [];
            btnStart.style.display = 'flex';
            faceFrame.classList.remove('active', 'scanning');
            updateProgress(0);
            updateStatus('error', 'Gagal. Silakan coba lagi.');
        }

        // Helpers
        function updateStatus(type, text) {
            statusText.innerText = text;
            statusDot.className = 'status-dot'; // reset
            if (type === 'ready' || type === 'success') statusDot.classList.add('ready');
            if (type === 'loading') statusDot.style.background = '#fbbf24'; // yellow
            if (type === 'error') statusDot.style.background = '#ef4444'; // red
        }

        let warningTimeout;
        function showWarning(msg) {
            const warningEl = document.getElementById('warningMessage');
            warningEl.innerText = msg;
            warningToast.classList.add('show');
            
            clearTimeout(warningTimeout);
            warningTimeout = setTimeout(() => {
                warningToast.classList.remove('show');
            }, 3000);
        }
        
        function hideWarning() {
            warningToast.classList.remove('show');
        }

    </script>
@endpush
