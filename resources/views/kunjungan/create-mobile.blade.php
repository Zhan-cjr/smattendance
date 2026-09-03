@extends('layouts.mobile.modern')

@section('title', 'Tambah Kunjungan')

@section('header_left')
    <a href="{{ route('kunjungan.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background: #e6fcf5 !important;
        }

        /* Premium Camera UI (No outer card) */
        .webcam-capture {
            width: 100%;
            height: 380px;
            border-radius: 24px;
            overflow: hidden;
            background: #1e293b;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 30px rgba(50, 116, 94, 0.12);
            margin-bottom: 25px;
            border: 2px solid #ffffff;
        }

        .webcam-capture video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            transform: scaleX(-1); /* Mirror for front camera */
        }

        /* Glassmorphism Overlays */
        .glass-overlay {
            position: absolute;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 12px;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            z-index: 20;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .overlay-date { top: 15px; left: 15px; }
        .overlay-time { top: 15px; right: 15px; }

        /* Camera Controls */
        .camera-controls {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 12px;
            z-index: 30;
            padding: 0 20px;
        }

        .btn-camera-action {
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s;
            border: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .btn-capture {
            background: #32745e;
            color: white;
            flex: 1;
        }

        .btn-switch {
            background: #ffffff;
            color: #32745e;
            width: 50px;
            flex-shrink: 0;
        }

        .btn-camera-action:active {
            transform: scale(0.95);
        }

        /* Image Preview Overlay */
        .preview-overlay {
            position: absolute;
            top: 60px;
            right: 15px;
            width: 80px;
            height: 80px;
            border-radius: 14px;
            border: 3px solid #fff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            z-index: 40;
            overflow: hidden;
            background: #eee;
            display: none;
            animation: popIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes popIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .preview-overlay img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Form Styling (Sync with Izin Absen) */
        .form-label-group {
            position: relative;
            margin-bottom: 15px;
            background: #ffffff;
            border: 1px solid #32745e;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 15px;
            top: 15px;
            font-size: 24px;
            color: #32745e;
            z-index: 10;
            pointer-events: none;
        }

        .form-label-group textarea {
            width: 100% !important;
            min-height: 120px !important;
            padding: 30px 15px 5px 52px !important;
            font-size: 16px;
            font-weight: 500;
            color: #2a6350;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            display: block !important;
            resize: none;
            line-height: 1.5;
        }

        .form-label-group label {
            position: absolute;
            top: 15px;
            left: 52px;
            font-size: 16px;
            color: #32745e;
            opacity: 0.8;
            pointer-events: none;
            transition: all 0.2s ease-in-out;
            margin-bottom: 0;
            z-index: 5;
        }

        .form-label-group textarea:focus ~ label,
        .form-label-group textarea:not(:placeholder-shown) ~ label {
            top: 5px;
            left: 52px;
            font-size: 11px;
            font-weight: 600;
            color: #32745e;
        }

        /* Modern Submit Button */
        .btn-submit-premium {
            width: 100%;
            height: 54px;
            background: #32745e;
            color: #ffffff;
            border: none;
            border-radius: 18px;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(50, 116, 94, 0.25);
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-submit-premium:active {
            transform: scale(0.97);
            background: #2a6350;
        }

        .error-hint {
            color: #ef4444;
            font-size: 12px;
            font-weight: 600;
            padding-left: 10px;
            margin-top: -15px;
            margin-bottom: 15px;
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="fade-up" style="padding: 10px 5px 100px 5px;">
        <form action="{{ route('kunjungan.store') }}" method="POST" enctype="multipart/form-data" id="formKunjungan">
            @csrf

            <!-- Hidden Fields -->
            @if (auth()->user()->hasRole('karyawan'))
                <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
            @endif
            <input type="hidden" name="foto" id="fotoData" value="">
            <input type="hidden" name="lokasi" id="lokasiData" value="">
            <input type="hidden" name="tanggal_kunjungan" value="{{ date('Y-m-d') }}">

            <!-- Camera Section (Floating Style) -->
            <div class="relative">
                <div class="webcam-capture">
                    {{-- Video Holder --}}
                    <div id="video-holder" class="absolute inset-0 z-0"></div>

                    {{-- Overlays --}}
                    <div class="glass-overlay overlay-date">{{ DateToIndo(date('Y-m-d')) }}</div>
                    <div class="glass-overlay overlay-time"><span id="jam-inner" class="jam-display">00:00:00</span></div>
                    
                    {{-- Image Preview Overlay --}}
                    <div id="imagePreview" class="preview-overlay">
                        <img id="previewImg" src="" alt="Preview">
                    </div>

                    {{-- Camera Placeholder --}}
                    <div id="cameraPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-white/20 z-0">
                        <ion-icon name="camera-outline" class="text-6xl mb-2"></ion-icon>
                        <span class="text-xs font-semibold uppercase tracking-widest">Initializing...</span>
                    </div>

                    {{-- Action Buttons Overlay --}}
                    <div class="camera-controls">
                        <button type="button" class="btn-camera-action btn-capture" id="btnScan">
                            <ion-icon name="camera-outline" class="text-xl"></ion-icon>
                            <span>Ambil Foto</span>
                        </button>
                        <button type="button" class="btn-camera-action btn-switch" id="btnSwitch">
                            <ion-icon name="camera-reverse-outline" class="text-xl"></ion-icon>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Description Field -->
            <div class="form-label-group shadow-sm">
                <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                <textarea name="deskripsi" id="deskripsi" placeholder=" " required>{{ old('deskripsi') }}</textarea>
                <label for="deskripsi">Deskripsi Kunjungan</label>
            </div>
            @error('deskripsi')
                <span class="error-hint">{{ $message }}</span>
            @enderror

            <!-- Location Status Indicator -->
            <div id="locationStatus" style="padding: 12px 15px; border-radius: 12px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; background: #fef3c7; border: 1px solid #fcd34d;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="location-outline" id="locationIcon" style="font-size: 20px; color: #d97706;"></ion-icon>
                    <span id="locationText" style="font-size: 14px; font-weight: 600; color: #92400e;">Mendeteksi lokasi...</span>
                </div>
                <button type="button" id="btnRetryLocation" style="background: transparent; border: none; color: #d97706; cursor: pointer; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; display: none;">Coba Lagi</button>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn-submit-premium" id="btnSimpan">
                <ion-icon name="send-outline" class="text-xl"></ion-icon>
                <span>Simpan Kunjungan</span>
            </button>
        </form>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            let stream = null;
            let currentFacingMode = 'user'; 
            let capturedImage = null;
            let locationDetected = false;

            // Update Time
            function updateJam() {
                const now = new Date();
                const jamStr = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                });
                $('.jam-display').text(jamStr);
            }
            setInterval(updateJam, 1000);
            updateJam();

            // Camera Engine
            function startCamera() {
                const constraints = {
                    video: { 
                        facingMode: currentFacingMode,
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                };

                navigator.mediaDevices.getUserMedia(constraints)
                    .then(function(mediaStream) {
                        stream = mediaStream;
                        const video = document.createElement('video');
                        video.srcObject = stream;
                        video.autoplay = true;
                        video.playsInline = true;
                        
                        // Apply mirror effect for front camera
                        video.style.width = '100%';
                        video.style.height = '100%';
                        video.style.objectFit = 'cover';
                        video.style.transform = currentFacingMode === 'user' ? 'scaleX(-1)' : 'none';

                        $('#video-holder').html(video);
                        $('#cameraPlaceholder').fadeOut();

                    }).catch(function(err) {
                        console.error('Camera Access Error:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Gagal mengakses kamera. Pastikan izin sudah diaktifkan.',
                        });
                    });
            }

            // Capture Logic
            $('#btnScan').on('click', function() {
                const video = $('#video-holder video')[0];
                if (!video) return;

                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');

                // Mirror correction for scan
                if (currentFacingMode === 'user') {
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                }
                
                ctx.drawImage(video, 0, 0);
                capturedImage = canvas.toDataURL('image/jpeg', 0.82);

                $('#previewImg').attr('src', capturedImage);
                $('#imagePreview').show();
                $('#fotoData').val(capturedImage);

                Swal.fire({
                    icon: 'success',
                    title: 'Tersimpan!',
                    text: 'Foto berhasil diambil.',
                    timer: 1500,
                    showConfirmButton: false
                });
            });

            // Switch Logic
            $('#btnSwitch').on('click', function() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
                startCamera();
            });

            // Geolocation
            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const loc = pos.coords.latitude + "," + pos.coords.longitude;
                            $('#lokasiData').val(loc);
                            locationDetected = true;
                            Swal.fire({
                                icon: 'success',
                                title: 'Lokasi Terdeteksi',
                                text: 'Koordinat lokasi sudah disimpan.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        (err) => {
                            locationDetected = false;
                            let errorMessage = 'Gagal mendeteksi lokasi.';
                            if (err.code === err.PERMISSION_DENIED) {
                                errorMessage = 'Izin lokasi ditolak. Aktifkan izin lokasi di pengaturan.';
                            } else if (err.code === err.POSITION_UNAVAILABLE) {
                                errorMessage = 'Layanan lokasi tidak tersedia.';
                            } else if (err.code === err.TIMEOUT) {
                                errorMessage = 'Timeout. Coba lagi setelah beberapa saat.';
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Lokasi Gagal',
                                text: errorMessage,
                            });
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                } else {
                    locationDetected = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Geolocation Tidak Tersedia',
                        text: 'Browser ini tidak mendukung geolocation.'
                    });
                }
            }

            // Init
            startCamera();
            getLocation();

            // Intercept form
            $('#formKunjungan').on('submit', function(e) {
                const desc = $('#deskripsi').val().trim();
                const foto = $('#fotoData').val();
                const lokasi = $('#lokasiData').val();

                if (!locationDetected || !lokasi) {
                    e.preventDefault();
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Lokasi Belum Terdeteksi', 
                        text: 'Sistem belum dapat mendeteksi lokasi Anda. Pastikan GPS aktif dan izin lokasi telah diberikan. Coba refresh halaman untuk mendeteksi ulang.' 
                    });
                    return false;
                }

                if (!foto) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Belum Ada Foto', text: 'Silakan ambil foto kunjungan terlebih dahulu.' });
                    return false;
                }
                if (!desc) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Data Kurang', text: 'Silakan isi deskripsi kunjungan.' });
                    return false;
                }
                
                $('#btnSimpan').addClass('opacity-50').attr('disabled', 'disabled').html('<ion-icon name="refresh-outline" class="animate-spin text-xl"></ion-icon><span>Menyimpan...</span>');
            });

            // Location Status UI Handler
            function updateLocationUI() {
                const statusDiv = $('#locationStatus');
                const icon = $('#locationIcon');
                const text = $('#locationText');
                const retryBtn = $('#btnRetryLocation');

                if (locationDetected) {
                    statusDiv.css({ 'background': '#d1fae5', 'border-color': '#6ee7b7' });
                    icon.attr('name', 'checkmark-circle-outline').css('color', '#059669');
                    text.text('Lokasi Terdeteksi').css('color', '#047857');
                    retryBtn.hide();
                } else {
                    statusDiv.css({ 'background': '#fee2e2', 'border-color': '#fecaca' });
                    icon.attr('name', 'alert-circle-outline').css('color', '#dc2626');
                    text.text('Lokasi Belum Terdeteksi').css('color', '#7f1d1d');
                    retryBtn.show();
                }
            }

            // Retry Location Button
            $('#btnRetryLocation').on('click', function(e) {
                e.preventDefault();
                $('#locationText').text('Mendeteksi lokasi...');
                $('#btnRetryLocation').hide();
                getLocation();
            });

            // Monitor location changes
            setInterval(updateLocationUI, 500);

            // Auto-resize
            $('#deskripsi').on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Cleanup
            $(window).on('beforeunload', function() {
                if (stream) stream.getTracks().forEach(track => track.stop());
            });
        });
    </script>
@endpush
