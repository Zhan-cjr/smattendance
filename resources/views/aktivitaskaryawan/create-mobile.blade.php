@extends('layouts.mobile.modern')

@section('title', 'Tambah Aktivitas')

@section('header_left')
    <a href="{{ route('aktivitaskaryawan.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
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
            max-width: 98vw;
            aspect-ratio: 3 / 4;
            margin: 0 auto;
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
            flex-direction: column;
        }

        .webcam-capture video,
        .webcam-capture canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            transform: scaleX(-1); /* Mirror for front camera */
            border-radius: 24px !important;
        }
        
        #video-holder {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
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

        /* Form Styling */
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

        /* Select dropdown styling */
        #jenis_aktivitas {
            cursor: pointer;
        }

        #jenis_aktivitas:focus {
            outline: none !important;
        }

        /* Custom activity input styling */
        #custom_activity {
            font-weight: 500;
            color: #2a6350;
        }

        #custom_activity:focus {
            outline: none !important;
            border-color: #32745e !important;
            box-shadow: 0 0 0 3px rgba(50, 116, 94, 0.1) !important;
        }

        #custom_activity::placeholder {
            color: #32745e;
            opacity: 0.5;
        }
    </style>
@endpush

@section('content')
    <div class="fade-up" style="padding: 10px 5px 100px 5px;">
        <form method="POST" action="{{ route('aktivitaskaryawan.store') }}" id="formAktivitas" enctype="multipart/form-data">
            @csrf
            
            <input type="hidden" name="foto" id="image-data">
            <input type="hidden" name="lokasi" id="lokasi">
            <input type="hidden" name="jenis_aktivitas_final" id="jenis_aktivitas_final">

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

            <!-- Jenis Aktivitas Dropdown -->
            <div class="form-label-group shadow-sm">
                <ion-icon name="list-outline" class="input-icon"></ion-icon>
                <select name="jenis_aktivitas" id="jenis_aktivitas" required style="appearance: none; -webkit-appearance: none; -moz-appearance: none; padding: 15px 5px 5px 52px !important; font-size: 16px; font-weight: 500; color: #2a6350; background: transparent !important; border: none !important; outline: none !important; box-shadow: none !important; width: 100%; height: 50px; display: block;">
                    <option value="" disabled selected>Pilih jenis aktivitas</option>
                    <option value="Pengajian Rutin Mingguan">Pengajian Rutin Mingguan</option>
                    <option value="Pengajian Tahsin AlQur'an">Pengajian Tahsin AlQur'an</option>
                    <option value="Lainnya">Lainnya (Tuliskan Manual)</option>
                </select>
                <div id="custom_activity_wrapper" style="display: none; margin-top: 15px;">
                    <input type="text" id="custom_activity" placeholder="Masukkan jenis aktivitas lainnya" style="width: 100%; padding: 15px; border: 1px solid #32745e; border-radius: 12px; font-size: 16px; color: #2a6350;">
                </div>
            </div>
            @error('jenis_aktivitas')
                <span class="error-hint">{{ $message }}</span>
            @enderror

            <button type="submit" class="btn-submit-premium" id="btnSubmit">
                <ion-icon name="cloud-upload-outline" class="text-xl"></ion-icon>
                <span>Simpan Aktivitas</span>
            </button>
        </form>
    </div>

    <canvas id="canvas" style="display: none;"></canvas>

@endsection

@push('myscript')
    <script>
        $(document).ready(function() {
            let video = null;
            let stream = null;
            let currentFacingMode = 'environment';
            let capturedImage = null;

            // Clock Synchronization
            function updateClock() {
                const now = new Date();
                const timeStr = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                });
                $('.jam-display').text(timeStr);
            }
            setInterval(updateClock, 1000);
            updateClock();

            // Handle jenis aktivitas dropdown
            $('#jenis_aktivitas').on('change', function() {
                const value = $(this).val();
                if (value === 'Lainnya') {
                    $('#custom_activity_wrapper').show();
                    $('#custom_activity').focus();
                } else {
                    $('#custom_activity_wrapper').hide();
                    $('#custom_activity').val('');
                }
            });

            // Camera Engine
            async function startCamera(facingMode) {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }

                const constraints = {
                    video: { 
                        facingMode: facingMode,
                        width: { ideal: 640 },
                        height: { ideal: 853 }
                    }
                };

                try {
                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                    const videoTag = document.createElement('video');
                    videoTag.srcObject = stream;
                    videoTag.autoplay = true;
                    videoTag.playsInline = true;
                    
                    // Style fitting untuk container dengan rasio 3:4
                    videoTag.style.width = '100%';
                    videoTag.style.height = '100%';
                    videoTag.style.objectFit = 'cover';
                    videoTag.style.transform = facingMode === 'user' ? 'scaleX(-1)' : 'none';

                    $('#video-holder').html(videoTag);
                    video = videoTag;
                    $('#cameraPlaceholder').fadeOut();
                    currentFacingMode = facingMode;
                } catch (err) {
                    console.error("Camera Access Error:", err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal mengakses kamera.',
                    });
                }
            }

            // Capture Logic with Watermark
            $('#btnScan').on('click', async function() {
                if (!video) return;

                // Validasi jenis_aktivitas harus dipilih
                const jenisAktivitas = $('#jenis_aktivitas').val();
                if (!jenisAktivitas || jenisAktivitas === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Jenis Aktivitas Dulu',
                        text: 'Silakan pilih jenis aktivitas sebelum mengambil foto.'
                    });
                    return;
                }

                // Validasi jika "Lainnya", custom_activity harus diisi
                if (jenisAktivitas === 'Lainnya') {
                    const customActivity = $('#custom_activity').val();
                    if (!customActivity || customActivity.trim() === '') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tuliskan Aktivitas Lainnya',
                            text: 'Silakan tuliskan jenis aktivitas lainnya sebelum mengambil foto.'
                        });
                        return;
                    }
                }

                try {
                    const canvas = document.getElementById('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    // Resolusi 3:4 (640x853)
                    canvas.width = 640;
                    canvas.height = 853;

                    // Calculate crop untuk menjaga aspect ratio dari video
                    const videoAspect = video.videoWidth / video.videoHeight;
                    const canvasAspect = 640 / 853; // 3:4
                    
                    let srcX, srcY, srcWidth, srcHeight;
                    
                    if (videoAspect > canvasAspect) {
                        // Video lebih lebar, crop dari samping
                        srcHeight = video.videoHeight;
                        srcWidth = video.videoHeight * canvasAspect;
                        srcX = (video.videoWidth - srcWidth) / 2;
                        srcY = 0;
                    } else {
                        // Video lebih tinggi, crop dari atas/bawah
                        srcWidth = video.videoWidth;
                        srcHeight = video.videoWidth / canvasAspect;
                        srcX = 0;
                        srcY = (video.videoHeight - srcHeight) / 2;
                    }

                    // Mirror correction for capture
                    if (currentFacingMode === 'user') {
                        ctx.translate(canvas.width, 0);
                        ctx.scale(-1, 1);
                    }
                    
                    // Draw gambar dari video dengan crop proporsional
                    ctx.drawImage(video, srcX, srcY, srcWidth, srcHeight, 0, 0, 640, 853);
                    
                    // Restore canvas transform
                    if (currentFacingMode === 'user') {
                        ctx.setTransform(1, 0, 0, 1, 0, 0);
                    }
                    
                    // Add watermark dan tunggu selesai
                    await addWatermark(ctx, canvas.width, canvas.height);
                    
                    capturedImage = canvas.toDataURL('image/jpeg', 0.95);

                    $('#previewImg').attr('src', capturedImage);
                    $('#imagePreview').show();
                    $('#image-data').val(capturedImage);

                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } catch (error) {
                    console.error('Capture error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat mengambil foto'
                    });
                }
            });

            // Add Watermark Function dengan Promise (seperti Absen)
            function addWatermark(ctx, width, height) {
                return new Promise((resolve) => {
                    // Get jenis aktivitas untuk title
                    const jenisAktivitas = $('#jenis_aktivitas').val();
                    const customActivity = $('#custom_activity').val();
                    const displayActivity = jenisAktivitas === 'Lainnya' ? customActivity : jenisAktivitas;

                    // Title style (jenis aktivitas di atas dengan highlight hijau)
                    const watermarkColor = 'rgba(40, 167, 69, 0.8)'; // Hijau terang (sama seperti Absen Masuk)
                    const textColor = '#FFFFFF';
                    const titleFontSize = height * 0.04;
                    const textRectPadding = width * 0.02;
                    const textRectRadius = 10;
                    const textRectX = width * 0.03;
                    const textRectY = width * 0.02;

                    // Buat box untuk title dengan rounded corners
                    ctx.fillStyle = watermarkColor;
                    ctx.font = `bold ${titleFontSize}px Poppins, Arial`;
                    const titleTextMetrics = ctx.measureText(displayActivity);
                    const titleTextWidth = titleTextMetrics.width + textRectPadding * 2;
                    const titleTextHeight = titleFontSize + textRectPadding;

                    // Draw rounded rectangle untuk title
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
                    
                    // Draw title text
                    ctx.fillStyle = textColor;
                    ctx.fillText(displayActivity, textRectX + textRectPadding, textRectY + titleFontSize);

                    // Bottom watermark info
                    const iconSize = height * 0.035;
                    const iconPadding = width * 0.02;
                    const margin = width * 0.04;
                    
                    const jamY = height * 0.76;
                    const tanggalY = jamY + (height * 0.05);
                    const lokasiY = tanggalY + (height * 0.05);
                    const namaKaryawanY = lokasiY + (height * 0.05);

                    const now = new Date();
                    const jam = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    const tanggal = '{{ DateToIndo(date('Y-m-d')) }}';
                    const lokasi = $('#lokasi').val() || 'Lokasi tidak terdeteksi';
                    const namaKaryawan = '{{ $karyawan->nama_karyawan ?? "Karyawan" }}';

                    // Load jam image
                    const imgJam = new Image();
                    const promiseJam = new Promise((resJam) => {
                        imgJam.onload = () => {
                            ctx.drawImage(imgJam, margin, jamY, iconSize, iconSize);
                            ctx.font = `bold ${height * 0.052}px Poppins, Arial`;
                            ctx.fillStyle = textColor;
                            ctx.textAlign = 'left';
                            ctx.shadowColor = 'rgba(0,0,0,0.3)';
                            ctx.shadowBlur = 2;
                            ctx.fillText(jam, margin + iconSize + iconPadding, jamY + iconSize);

                            ctx.font = `bold ${height * 0.032}px Poppins, Arial`;
                            ctx.fillText(tanggal, margin + iconSize + iconPadding, tanggalY + iconSize);
                            resJam();
                        };
                        imgJam.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
                    });

                    // Load lokasi image
                    const imgLokasi = new Image();
                    const promiseLokasi = new Promise((resLokasi) => {
                        imgLokasi.onload = () => {
                            ctx.drawImage(imgLokasi, margin, lokasiY, iconSize, iconSize);
                            ctx.font = `bold ${height * 0.032}px Poppins, Arial`;
                            ctx.fillStyle = textColor;
                            ctx.fillText('Lokasi: ' + lokasi.substring(0, 18), margin + iconSize + iconPadding, lokasiY + iconSize);
                            resLokasi();
                        };
                        imgLokasi.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>';
                    });

                    // Load user image
                    const imgUser = new Image();
                    const promiseUser = new Promise((resUser) => {
                        imgUser.onload = () => {
                            ctx.drawImage(imgUser, margin, namaKaryawanY, iconSize, iconSize);
                            ctx.font = `bold ${height * 0.032}px Poppins, Arial`;
                            ctx.fillStyle = textColor;
                            ctx.fillText(namaKaryawan.substring(0, 20), margin + iconSize + iconPadding, namaKaryawanY + iconSize);
                            resUser();
                        };
                        imgUser.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';
                    });

                    // Tunggu semua images selesai load
                    Promise.all([promiseJam, promiseLokasi, promiseUser]).then(() => {
                        // Draw footer text
                        const footerText = 'Waktu dan Lokasi diverifikasi oleh SM Attendance';
                        ctx.font = `italic ${height * 0.022}px Poppins, Arial`;
                        ctx.fillStyle = 'rgba(255,255,255,0.9)';
                        ctx.textAlign = 'right';
                        ctx.shadowColor = 'rgba(0,0,0,0.3)';
                        ctx.shadowBlur = 2;
                        ctx.fillText(footerText, width - margin, height - margin + 5);
                        
                        ctx.shadowColor = 'transparent';
                        resolve();
                    });
                });
            }

            // Save image function
            function saveImageToGallery(imageUri) {
                const link = document.createElement('a');
                link.href = imageUri;
                const jenisAkt = $('#jenis_aktivitas').val() || 'aktivitas';
                const timestamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                const waktu = new Date().toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/:/g, '');
                link.download = `Aktivitas_${jenisAkt}_${timestamp}_${waktu}.jpg`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            // Switch Camera
            $('#btnSwitch').on('click', function() {
                currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
                startCamera(currentFacingMode);
            });

            // Geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    $('#lokasi').val(position.coords.latitude + "," + position.coords.longitude);
                }, function(error) {
                    console.warn('Geolocation Error:', error.message);
                }, { enableHighAccuracy: true });
            }

            // Init
            startCamera(currentFacingMode);

            // Form Interceptor dengan AJAX
            $('#formAktivitas').on('submit', function(e) {
                const foto = $('#image-data').val();
                if (!foto) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Belum Ada Foto', text: 'Silakan ambil foto aktivitas Anda.' });
                    return false;
                }
                
                const jenisAktivitas = $('#jenis_aktivitas').val();
                if (!jenisAktivitas) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Jenis Aktivitas Belum Dipilih', text: 'Silakan pilih jenis aktivitas Anda.' });
                    return false;
                }

                // Handle custom activity
                if (jenisAktivitas === 'Lainnya') {
                    const customActivity = $('#custom_activity').val();
                    if (!customActivity || customActivity.trim() === '') {
                        e.preventDefault();
                        Swal.fire({ icon: 'warning', title: 'Aktivitas Lainnya Kosong', text: 'Silakan tuliskan jenis aktivitas Anda.' });
                        return false;
                    }
                    $('#jenis_aktivitas_final').val(customActivity);
                } else {
                    $('#jenis_aktivitas_final').val(jenisAktivitas);
                }
                
                const loc = $('#lokasi').val();
                if (!loc) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Lokasi Belum Terdeteksi', text: 'Tunggu sejenak agar lokasi berhasil dideteksi.' });
                    return false;
                }

                e.preventDefault();
                $('#btnSubmit').addClass('opacity-50').attr('disabled', 'disabled').html('<ion-icon name="sync-outline" class="animate-spin text-xl mr-2"></ion-icon><span>Menyimpan...</span>');

                // AJAX Submit
                const formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Download foto otomatis
                        saveImageToGallery(capturedImage);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Aktivitas berhasil disimpan dan foto telah diunduh',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('aktivitaskaryawan.index') }}";
                        });
                    },
                    error: function(xhr) {
                        $('#btnSubmit').removeClass('opacity-50').attr('disabled', false).html('<ion-icon name="cloud-upload-outline" class="text-xl"></ion-icon><span>Simpan Aktivitas</span>');
                        const responseJSON = xhr.responseJSON || {};
                        const message = responseJSON.message || 'Terjadi kesalahan saat menyimpan aktivitas';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: message
                        });
                    }
                });
            });



            // Cleanup
            $(window).on('beforeunload', function() {
                if (stream) stream.getTracks().forEach(track => track.stop());
            });
        });
    </script>
    <style>
        .animate-spin { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
@endpush
