@extends('layouts.mobile.modern')
@section('title', 'Form ' . ucfirst($tipe) . ' Toko')
@section('hide_nav', 'true')

@section('header_left')
    <a href="{{ route('checklist.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
<style>
    /* Sembunyikan bottom navigation agar tidak menghalangi tombol submit */
    .appBottomMenu, #bottomNav {
        display: none !important;
    }

    .point-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        transition: all 0.2s ease;
    }
    .dark .point-card {
        background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .point-card.completed {
        border-color: #10b981 !important;
        background: #f0fdf4 !important;
    }
    .dark .point-card.completed {
        background: rgba(16, 185, 129, 0.1) !important;
        border-color: rgba(16, 185, 129, 0.4) !important;
    }
    .point-title {
        color: #0f172a !important;
        font-weight: 700;
        font-size: 13px;
    }
    .dark .point-title {
        color: #f8fafc !important;
    }
    .point-desc {
        color: #475569 !important;
        font-size: 11px;
    }
    .dark .point-desc {
        color: #94a3b8 !important;
    }
    .preview-box {
        aspect-ratio: 4/3;
        border-radius: 14px;
        overflow: hidden;
        background: #0f172a;
        position: relative;
    }
    .floating-bar {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 10000;
        padding-bottom: max(16px, env(safe-area-inset-bottom)) !important;
        box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.12);
    }
    .dark .floating-bar {
        background: #0f172a !important;
        border-top: 1px solid rgba(255, 255, 255, 0.15) !important;
        box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.5);
    }
</style>
@endpush

@section('content')
<div class="px-2 pt-1 pb-52 max-w-md mx-auto">
    <!-- Header Banner -->
    <div class="mb-3 rounded-3xl p-4 text-white shadow-md {{ $tipe == 'opening' ? 'bg-gradient-to-r from-amber-600 to-amber-800' : 'bg-gradient-to-r from-indigo-700 via-indigo-800 to-slate-900' }}">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-white/20 text-white border border-white/20">
                {{ strtoupper($tipe) }} CHECKLIST &bull; {{ $karyawan->nama_dept ?? 'TOKO' }}
            </span>
            <span class="text-[11px] font-semibold text-white/90" id="liveClockText">
                {{ date('H:i') }} WIB
            </span>
        </div>
        <h3 class="text-base font-extrabold text-white">{{ $karyawan->nama_cabang ?? 'Kantor / Toko' }} &bull; {{ $karyawan->nama_dept ?? $karyawan->kode_dept }}</h3>
        <p class="text-xs text-white/80 mt-0.5">
            Petugas: <strong>{{ $karyawan->nama_karyawan }}</strong> (Dept: {{ $karyawan->nama_dept ?? $karyawan->kode_dept }})
        </p>

        <!-- Live GPS Radius Status -->
        <div class="mt-3 p-2 rounded-xl bg-black/25 backdrop-blur-md flex items-center justify-between text-[11px]">
            <div class="flex items-center gap-1.5">
                <i class="fa-solid fa-location-dot text-amber-300"></i>
                <span id="gpsStatusLabel" class="font-medium text-white/90">Mencari koordinat GPS...</span>
            </div>
            <button type="button" onclick="detectGps()" class="text-[10px] font-bold text-amber-200 underline">Refresh</button>
        </div>
    </div>

    <!-- Draft Restored Banner (Muncul otomatis jika ada draft foto tersimpan) -->
    <div id="draftRestoredBanner" class="hidden mb-3 p-3 rounded-2xl bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-teal-600 dark:text-teal-400 text-sm"></i>
            <div>
                <span class="text-xs font-bold text-teal-950 dark:text-teal-100 block">Draf Foto Tersimpan Otomatis</span>
                <span id="draftRestoredText" class="text-[10.5px] text-teal-800 dark:text-teal-300">Foto Anda sebelumnya telah dipulihkan.</span>
            </div>
        </div>
        <button type="button" onclick="confirmResetDraft()" class="px-2.5 py-1 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 text-[10.5px] font-bold active:scale-95 transition-all">
            Reset Draf
        </button>
    </div>

    <!-- Landscape Photo Instruction Banner -->
    <div class="mb-3.5 p-3 rounded-2xl bg-gradient-to-r from-amber-500/15 via-orange-500/10 to-amber-500/5 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-700/60 shadow-xs flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
            <i class="fa-solid fa-mobile-screen fa-rotate-90 text-lg"></i>
        </div>
        <div class="leading-tight">
            <span class="text-xs font-black text-amber-900 dark:text-amber-200 block uppercase tracking-wide">
                WAJIB FOTO POSISI LANDSCAPE (HP MIRING)
            </span>
            <p class="text-[11px] text-amber-800 dark:text-amber-300/90 mt-0.5">
                Pegang HP secara <strong>horizontal/miring</strong> saat memotret agar seluruh area tampak luas dan masuk ke grid WhatsApp <strong>100% utuh tanpa terpotong</strong>.
            </p>
        </div>
    </div>

    <!-- Stepper / Progress Counter -->
    <div class="mb-4 p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Kelengkapan Foto</span>
            <h4 class="text-xs font-extrabold text-slate-800 dark:text-slate-100">
                <span id="completedCount" class="text-teal-600 dark:text-teal-400 font-black">0</span> dari {{ $checkpoints->count() }} Titik Selesai
            </h4>
        </div>
        <div class="w-10 h-10 rounded-full border-4 border-slate-200 dark:border-slate-700 flex items-center justify-center font-black text-xs text-slate-700 dark:text-slate-200 relative" id="progressRing">
            <span id="progressPercent">0%</span>
        </div>
    </div>

    <!-- Checkpoint Form -->
    <form id="checklistForm" method="POST" action="{{ route('checklist.store', [], false) }}" onsubmit="submitForm(event)">
        @csrf
        <input type="hidden" name="tipe" value="{{ $tipe }}">
        <input type="hidden" name="latitude" id="inputLat">
        <input type="hidden" name="longitude" id="inputLng">
        <input type="hidden" name="lokasi_nama" id="inputLokasi" value="{{ $karyawan->nama_cabang }} - {{ $karyawan->nama_dept ?? $karyawan->kode_dept }}">

        <div class="space-y-4">
            @foreach($checkpoints as $index => $cp)
                @php
                    $num = $index + 1;
                @endphp
                <div class="point-card p-3.5 shadow-2xs" id="cardPoint_{{ $cp->id }}">
                    <!-- Header Titik -->
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex items-start gap-2.5">
                            <span class="w-6 h-6 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5 border border-teal-200 dark:border-teal-800">
                                {{ $num }}
                            </span>
                            <div>
                                <h4 class="point-title leading-tight">
                                    {{ $cp->nama_checkpoint }}
                                </h4>
                                @if($cp->deskripsi)
                                    <p class="point-desc mt-0.5 leading-snug">
                                        {{ $cp->deskripsi }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <span class="status-badge px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 shrink-0" id="badge_{{ $cp->id }}">
                            Wajib Foto
                        </span>
                    </div>

                    <!-- Hidden Input for Photo Data -->
                    <input type="hidden" name="photos[{{ $cp->id }}]" id="photoData_{{ $cp->id }}" required>

                    <!-- Hidden Native Camera File Input -->
                    <input type="file" accept="image/*" capture="environment" id="fileInput_{{ $cp->id }}" class="hidden" onchange="handleFileCapture(event, {{ $cp->id }}, '{{ addslashes($cp->nama_checkpoint) }}')">

                    <!-- Preview & Action Box -->
                    <div class="mt-2.5">
                        <div id="previewContainer_{{ $cp->id }}" class="preview-box hidden mb-2">
                            <img id="imgPreview_{{ $cp->id }}" class="w-full h-full object-cover">
                            <!-- Retake Floating Button -->
                            <button type="button" onclick="triggerCamera({{ $cp->id }})" class="absolute bottom-2 right-2 px-3 py-1.5 rounded-xl bg-black/75 backdrop-blur-md text-white text-[10.5px] font-bold flex items-center gap-1.5 shadow-md active:scale-95 transition-all">
                                <i class="fa-solid fa-arrows-rotate"></i>
                                <span>Foto Ulang</span>
                            </button>
                        </div>

                        <!-- Initial Take Photo Button -->
                        <button type="button" id="btnCapture_{{ $cp->id }}" onclick="triggerCamera({{ $cp->id }})" class="w-full py-2.5 px-3 rounded-xl border border-dashed border-teal-500/60 dark:border-teal-400/40 bg-teal-50/50 dark:bg-teal-950/20 text-teal-700 dark:text-teal-300 font-bold text-xs flex items-center justify-center gap-2 active:scale-98 transition-all hover:bg-teal-50">
                            <i class="fa-solid fa-mobile-screen fa-rotate-90 text-sm text-amber-500"></i>
                            <span>Ambil Foto Landscape (HP Miring)</span>
                        </button>
                    </div>

                    <!-- Optional Note Field -->
                    <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <input type="text" name="notes[{{ $cp->id }}]" id="noteInput_{{ $cp->id }}" oninput="autoSaveDraft()" placeholder="Catatan temuan (opsional, misal: gembok sudah diganti)" class="w-full text-[11px] px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-teal-500">
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Floating Submit Bar (Full width, positioned above all content, without bottomNav) -->
        <div class="floating-bar p-3.5 max-w-md mx-auto shadow-2xl">
            <!-- Mandatory Disclaimer Checkbox -->
            <label class="flex items-start gap-2.5 mb-2.5 cursor-pointer select-none">
                <input type="checkbox" name="disclaimer_agreed" id="disclaimerCheckbox" required value="1" onchange="handleDisclaimerChange()" class="mt-0.5 w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300">
                <span class="text-[10px] text-slate-700 dark:text-slate-300 font-medium leading-tight">
                    <strong>Pernyataan:</strong> Saya menyatakan dengan sesungguhnya bahwa seluruh area telah diperiksa langsung tanpa ada yang terlewat. Saya bertanggung jawab penuh atas kebenaran laporan ini apabila terjadi kerugian atau kelalaian pemeriksaan.
                </span>
            </label>

            <!-- Submit Button -->
            <button type="submit" id="btnSubmit" disabled class="w-full py-3.5 rounded-2xl text-xs font-black uppercase tracking-wider text-white flex items-center justify-center gap-2 shadow-lg transition-all opacity-50 cursor-not-allowed" style="background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #14b8a6 100%);">
                <i class="fa-solid fa-paper-plane" id="submitIcon"></i>
                <span id="submitText">Kirim Laporan & Generate Grid WA</span>
            </button>
        </div>
    </form>
</div>

<!-- Hidden Canvas for Watermarking -->
<canvas id="watermarkCanvas" class="hidden"></canvas>
@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const totalCheckpoints = {{ $checkpoints->count() }};
    const employeeName = @json($karyawan->nama_karyawan);
    const branchName = @json($karyawan->nama_cabang ?? 'Kantor / Toko');
    const deptName = @json($karyawan->nama_dept ?? $karyawan->kode_dept ?? 'Toko');
    const draftKey = 'smatt3_chk_draft_{{ $tipe }}_{{ $karyawan->nik }}_{{ $karyawan->kode_dept }}_{{ date("Y-m-d") }}';
    const photoBlobs = {}; // In-memory compressed binary blobs for instant upload
    let userLat = null;
    let userLng = null;
    let formSubmitted = false;

    function dataURLtoBlob(dataurl) {
        try {
            const arr = dataurl.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new Blob([u8arr], {type: mime});
        } catch (e) {
            return null;
        }
    }

    // IndexedDB Helper for Storing Large Base64 Photos reliably
    const idbHelper = {
        dbName: 'SmattChecklistDB',
        storeName: 'draftStore',
        openDB: function() {
            return new Promise((resolve, reject) => {
                const req = indexedDB.open(this.dbName, 1);
                req.onupgradeneeded = function(e) {
                    const db = e.target.result;
                    if (!db.objectStoreNames.contains('draftStore')) {
                        db.createObjectStore('draftStore');
                    }
                };
                req.onsuccess = () => resolve(req.result);
                req.onerror = () => reject(req.error);
            });
        },
        saveDraft: function(data) {
            return new Promise(async (resolve) => {
                try {
                    const db = await this.openDB();
                    const tx = db.transaction(this.storeName, 'readwrite');
                    tx.objectStore(this.storeName).put(data, draftKey);
                    tx.oncomplete = () => resolve(true);
                    tx.onerror = () => resolve(false);
                } catch (e) {
                    // Fallback to localStorage if IndexedDB fails
                    try { localStorage.setItem(draftKey, JSON.stringify(data)); } catch (err) {}
                    resolve(false);
                }
            });
        },
        getDraft: async function() {
            try {
                const db = await this.openDB();
                return new Promise((resolve) => {
                    const tx = db.transaction(this.storeName, 'readonly');
                    const req = tx.objectStore(this.storeName).get(draftKey);
                    req.onsuccess = () => resolve(req.result);
                    req.onerror = () => resolve(null);
                });
            } catch (e) {
                try {
                    const ls = localStorage.getItem(draftKey);
                    return ls ? JSON.parse(ls) : null;
                } catch (err) {
                    return null;
                }
            }
        },
        deleteDraft: async function() {
            try {
                const db = await this.openDB();
                const tx = db.transaction(this.storeName, 'readwrite');
                tx.objectStore(this.storeName).delete(draftKey);
            } catch (e) {}
            try { localStorage.removeItem(draftKey); } catch (e) {}
        }
    };

    document.addEventListener('DOMContentLoaded', async () => {
        detectGps();
        updateClock();
        setInterval(updateClock, 30000);

        // Restore draft from cache automatically
        await restoreDraft();

        // Warn if navigating away with unsaved photos
        window.addEventListener('beforeunload', (e) => {
            const completed = countCompletedPhotos();
            if (completed > 0 && !formSubmitted) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Ensure auto-save on switching tabs/apps or navigating away
        window.addEventListener('pagehide', () => autoSaveDraft(true));
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) autoSaveDraft(true);
        });
    });

    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const clockEl = document.getElementById('liveClockText');
        if (clockEl) clockEl.innerText = `${h}:${m} WIB`;
    }

    function detectGps() {
        const label = document.getElementById('gpsStatusLabel');
        if (!navigator.geolocation) {
            label.innerText = 'GPS tidak didukung perangkat ini.';
            return;
        }
        label.innerText = 'Mencari sinyal GPS...';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                userLat = pos.coords.latitude.toFixed(6);
                userLng = pos.coords.longitude.toFixed(6);
                document.getElementById('inputLat').value = userLat;
                document.getElementById('inputLng').value = userLng;
                label.innerHTML = `GPS Terkunci: <strong>${userLat}, ${userLng}</strong> (±${Math.round(pos.coords.accuracy)}m)`;
            },
            (err) => {
                label.innerText = 'GPS offline / izin lokasi ditolak.';
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    function triggerCamera(id) {
        const fileInput = document.getElementById('fileInput_' + id);
        if (fileInput) {
            fileInput.value = '';
            fileInput.click();
        }
    }

    function handleFileCapture(event, id, pointName) {
        const file = event.target.files[0];
        if (!file) return;

        // Visual loading indicator on card badge
        const badge = document.getElementById('badge_' + id);
        if (badge) {
            badge.className = 'status-badge px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300';
            badge.innerHTML = '<i class="fa-solid fa-spinner animate-spin text-[8px]"></i> Kompresi...';
        }

        // Memory-efficient object URL (prevents huge base64 memory spikes on high-res phones)
        const objectUrl = URL.createObjectURL(file);
        const rawImg = new Image();

        rawImg.onload = function() {
            URL.revokeObjectURL(objectUrl);
            applyClientWatermark(rawImg, pointName, function(blob, base64) {
                photoBlobs[id] = blob;
                setPhotoToCard(id, base64);
                autoSaveDraft(true);
                checkFormReady();
            });
        };

        rawImg.onerror = function() {
            URL.revokeObjectURL(objectUrl);
            if (badge) {
                badge.className = 'status-badge px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-red-100 text-red-800';
                badge.innerHTML = 'Gagal';
            }
            Swal.fire('Gagal Memuat Foto', 'Format foto tidak didukung oleh browser.', 'error');
        };

        rawImg.src = objectUrl;
    }

    function setPhotoToCard(id, base64) {
        const previewImg = document.getElementById('imgPreview_' + id);
        const container = document.getElementById('previewContainer_' + id);
        const btnCapture = document.getElementById('btnCapture_' + id);
        const hiddenInp = document.getElementById('photoData_' + id);
        const card = document.getElementById('cardPoint_' + id);
        const badge = document.getElementById('badge_' + id);

        if (previewImg && container && hiddenInp) {
            previewImg.src = base64;
            container.classList.remove('hidden');
            if (btnCapture) btnCapture.classList.add('hidden');
            hiddenInp.value = base64;

            if (card) card.classList.add('completed');
            if (badge) {
                badge.className = 'status-badge px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300';
                badge.innerHTML = '<i class="fa-solid fa-check text-[8px]"></i> Terfoto';
            }
        }
    }

    function applyClientWatermark(img, pointName, callback) {
        const canvas = document.getElementById('watermarkCanvas');
        const ctx = canvas.getContext('2d');

        // Always normalize to Landscape 4:3 (800x600) for zero crop and fast upload
        const targetW = 800;
        const targetH = 600;
        canvas.width = targetW;
        canvas.height = targetH;

        const isLandscape = img.width >= img.height;

        if (isLandscape) {
            // Draw full landscape photo directly (100% uncropped!)
            ctx.drawImage(img, 0, 0, targetW, targetH);
        } else {
            // If user accidentally shot in portrait, contain without cropping ("rill foto semua")
            ctx.fillStyle = '#0f172a';
            ctx.fillRect(0, 0, targetW, targetH);

            const fitH = targetH;
            const fitW = Math.round((img.width * targetH) / img.height);
            const offsetX = Math.round((targetW - fitW) / 2);

            ctx.drawImage(img, offsetX, 0, fitW, fitH);
        }

        // ==========================================
        // 1. TOP WATERMARK (Seperti Absensi)
        // ==========================================
        const topMargin = 16;
        const badgeH = 28;

        // Top-Left Badge 1: Tipe Checklist
        const tipeUpper = '{{ strtoupper($tipe) }}';
        const badgeTypeW = 158;
        ctx.fillStyle = '{{ $tipe == "opening" ? "rgba(217, 119, 6, 0.95)" : "rgba(67, 56, 202, 0.95)" }}';
        ctx.beginPath();
        ctx.roundRect(topMargin, topMargin, badgeTypeW, badgeH, 6);
        ctx.fill();

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 12px sans-serif';
        ctx.fillText('● CHECKLIST ' + tipeUpper, topMargin + 10, topMargin + 19);

        // Top-Left Badge 2: Nama Checkpoint
        const pointLabel = pointName.substring(0, 32);
        ctx.font = 'bold 12.5px sans-serif';
        const pointMetrics = ctx.measureText(pointLabel);
        const badgePointW = pointMetrics.width + 20;

        ctx.fillStyle = 'rgba(15, 23, 42, 0.90)';
        ctx.beginPath();
        ctx.roundRect(topMargin + badgeTypeW + 8, topMargin, badgePointW, badgeH, 6);
        ctx.fill();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.25)';
        ctx.lineWidth = 1;
        ctx.stroke();

        ctx.fillStyle = '#fef08a'; // Light Amber/Yellow
        ctx.fillText(pointLabel, topMargin + badgeTypeW + 18, topMargin + 19);

        // Top-Right Badge: TERVERIFIKASI (Hijau Terang Absensi)
        const verifW = 140;
        const verifX = targetW - topMargin - verifW;
        ctx.fillStyle = 'rgba(5, 150, 105, 0.95)'; // Emerald-600
        ctx.beginPath();
        ctx.roundRect(verifX, topMargin, verifW, badgeH, 6);
        ctx.fill();

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 12.5px sans-serif';
        ctx.fillText('✓ TERVERIFIKASI', verifX + 13, topMargin + 19);

        // ==========================================
        // 2. BOTTOM WATERMARK (Seperti Absensi)
        // ==========================================
        const bannerH = 112;
        const bannerY = targetH - bannerH;

        // Dark gradient container with high opacity for 100% contrast
        const grad = ctx.createLinearGradient(0, bannerY, 0, targetH);
        grad.addColorStop(0, 'rgba(15, 23, 42, 0.88)');
        grad.addColorStop(0.2, 'rgba(15, 23, 42, 0.96)');
        grad.addColorStop(1, 'rgba(15, 23, 42, 0.98)');
        ctx.fillStyle = grad;
        ctx.fillRect(0, bannerY, targetW, bannerH);

        // Amber/Indigo accent border on top of bottom banner
        ctx.fillStyle = '{{ $tipe == "opening" ? "#f59e0b" : "#6366f1" }}';
        ctx.fillRect(0, bannerY, targetW, 3);

        const now = new Date();
        const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const dateStr = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

        const botPadX = 18;

        // Row 1: Jam Besar & Tanggal (Sama persis Absensi)
        ctx.font = 'bold 22px sans-serif';
        ctx.fillStyle = '#ffffff';
        ctx.fillText(timeStr + ' WIB', botPadX, bannerY + 28);
        const timeW = ctx.measureText(timeStr + ' WIB').width;

        ctx.font = '500 13px sans-serif';
        ctx.fillStyle = '#cbd5e1';
        ctx.fillText(' • ' + dateStr, botPadX + timeW + 4, bannerY + 28);

        // Row 2: Lokasi & GPS
        const locText = userLat ? `${branchName} - ${deptName} (GPS: ${userLat}, ${userLng})` : `${branchName} - ${deptName}`;
        ctx.font = '500 12px sans-serif';
        ctx.fillStyle = '#67e8f9'; // Cyan
        ctx.fillText('📍 ' + locText, botPadX, bannerY + 50);

        // Row 3: Petugas / PIC
        ctx.font = '600 12.5px sans-serif';
        ctx.fillStyle = '#fde68a'; // Light Yellow
        ctx.fillText('👤 Petugas: ' + employeeName + ' (' + deptName + ')', botPadX, bannerY + 72);

        // Row 4: Verifikasi Footer (Persis baris 1700 di Absensi)
        ctx.font = 'italic 11px sans-serif';
        ctx.fillStyle = 'rgba(255, 255, 255, 0.75)';
        ctx.textAlign = 'right';
        ctx.fillText('Waktu dan Lokasi diverifikasi oleh SM Attendance', targetW - 18, bannerY + 98);
        ctx.textAlign = 'left';

        const base64 = canvas.toDataURL('image/jpeg', 0.75);
        canvas.toBlob((blob) => {
            if (callback) callback(blob, base64);
        }, 'image/jpeg', 0.75);
    }

    function countCompletedPhotos() {
        let count = 0;
        const inputs = document.querySelectorAll('input[id^="photoData_"]');
        inputs.forEach(inp => {
            if (inp.value && inp.value.length > 50) count++;
        });
        return count;
    }

    function checkFormReady() {
        const completed = countCompletedPhotos();

        document.getElementById('completedCount').innerText = completed;
        const percent = Math.round((completed / totalCheckpoints) * 100);
        document.getElementById('progressPercent').innerText = percent + '%';

        const disclaimerChecked = document.getElementById('disclaimerCheckbox').checked;
        const submitBtn = document.getElementById('btnSubmit');

        if (completed === totalCheckpoints && disclaimerChecked) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.add('active:scale-98');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.remove('active:scale-98');
        }
    }

    function handleDisclaimerChange() {
        autoSaveDraft();
        checkFormReady();
    }

    // Auto Save draft to cache (IndexedDB)
    let saveTimeout = null;
    function autoSaveDraft(immediate = false) {
        clearTimeout(saveTimeout);
        const executeSave = async () => {
            const draft = {
                photos: {},
                notes: {},
                disclaimer: document.getElementById('disclaimerCheckbox').checked,
                lat: userLat,
                lng: userLng,
                updatedAt: new Date().toISOString()
            };

            document.querySelectorAll('input[id^="photoData_"]').forEach(inp => {
                const id = inp.id.replace('photoData_', '');
                if (inp.value && inp.value.length > 50) {
                    draft.photos[id] = inp.value;
                }
            });

            document.querySelectorAll('input[id^="noteInput_"]').forEach(inp => {
                const id = inp.id.replace('noteInput_', '');
                if (inp.value) {
                    draft.notes[id] = inp.value;
                }
            });

            await idbHelper.saveDraft(draft);
        };

        if (immediate) {
            executeSave();
        } else {
            saveTimeout = setTimeout(executeSave, 400);
        }
    }

    // Restore draft when reopening form
    async function restoreDraft() {
        const draft = await idbHelper.getDraft();
        if (!draft) return;

        let restoredPhotosCount = 0;

        if (draft.photos) {
            for (const [id, base64] of Object.entries(draft.photos)) {
                if (base64 && base64.length > 50) {
                    setPhotoToCard(id, base64);
                    restoredPhotosCount++;
                }
            }
        }

        if (draft.notes) {
            for (const [id, note] of Object.entries(draft.notes)) {
                const noteInput = document.getElementById('noteInput_' + id);
                if (noteInput && note) {
                    noteInput.value = note;
                }
            }
        }

        if (draft.disclaimer) {
            document.getElementById('disclaimerCheckbox').checked = true;
        }

        checkFormReady();

        if (restoredPhotosCount > 0) {
            const banner = document.getElementById('draftRestoredBanner');
            const bannerText = document.getElementById('draftRestoredText');
            if (bannerText) {
                bannerText.innerText = `${restoredPhotosCount} foto yang diambil sebelumnya telah dipulihkan otomatis.`;
            }
            if (banner) banner.classList.remove('hidden');
        }
    }

    function confirmResetDraft() {
        Swal.fire({
            title: 'Hapus Draf Tersimpan?',
            text: 'Semua foto yang sudah diambil akan direset dan Anda perlu mengambil foto kembali dari awal.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                await idbHelper.deleteDraft();
                window.location.reload();
            }
        });
    }

    function submitForm(e) {
        e.preventDefault();

        const form = document.getElementById('checklistForm');
        const formData = new FormData();

        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? 
                          document.querySelector('meta[name="csrf-token"]').getAttribute('content') :
                          document.querySelector('input[name="_token"]').value;

        formData.append('_token', csrfToken);
        formData.append('tipe', '{{ $tipe }}');
        formData.append('latitude', document.getElementById('inputLat').value || '');
        formData.append('longitude', document.getElementById('inputLng').value || '');
        formData.append('lokasi_nama', document.getElementById('inputLokasi').value || '');
        formData.append('disclaimer_agreed', '1');

        // Notes
        document.querySelectorAll('input[id^="noteInput_"]').forEach(inp => {
            const id = inp.id.replace('noteInput_', '');
            if (inp.value) {
                formData.append('notes[' + id + ']', inp.value);
            }
        });

        // Photos as binary blobs (instant upload, tiny payload)
        let count = 0;
        document.querySelectorAll('input[id^="photoData_"]').forEach(inp => {
            const id = inp.id.replace('photoData_', '');
            if (photoBlobs[id]) {
                formData.append('photos[' + id + ']', photoBlobs[id], 'chk_' + id + '.jpg');
                count++;
            } else if (inp.value && inp.value.length > 50) {
                const blob = dataURLtoBlob(inp.value);
                if (blob) {
                    formData.append('photos[' + id + ']', blob, 'chk_' + id + '.jpg');
                    count++;
                }
            }
        });

        if (count < totalCheckpoints) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Lengkap',
                text: 'Harap lengkapi seluruh foto checkpoint sebelum mengirim.'
            });
            return;
        }

        const submitBtn = document.getElementById('btnSubmit');
        const submitText = document.getElementById('submitText');
        const submitIcon = document.getElementById('submitIcon');

        submitBtn.disabled = true;
        submitText.innerText = 'Mengirim Laporan...';
        submitIcon.className = 'fa-solid fa-spinner animate-spin';

        Swal.fire({
            title: 'Mengirim Laporan...',
            text: 'Mengunggah foto dan membuat kolase WhatsApp...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        // Use strictly relative URL path to prevent any cross-domain or DNS lookup issue
        const targetUrl = '{{ route("checklist.store", [], false) }}';

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 25000);

        fetch(targetUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            signal: controller.signal
        })
        .then(async (res) => {
            clearTimeout(timeoutId);
            const text = await res.text();
            let data = null;
            try {
                data = JSON.parse(text);
            } catch (err) {
                throw new Error('Respon server (Status ' + res.status + '): ' + text.substring(0, 100));
            }

            if (!res.ok || !data.success) {
                throw new Error(data.message || ('Gagal mengirim laporan (Status ' + res.status + ')'));
            }

            formSubmitted = true;
            await idbHelper.deleteDraft();

            Swal.fire({
                icon: 'success',
                title: 'Laporan Berhasil!',
                text: data.message,
                timer: 1600,
                showConfirmButton: false
            }).then(() => {
                window.location.href = data.redirect_url;
            });
        })
        .catch(err => {
            clearTimeout(timeoutId);
            console.error('Checklist submit error:', err);
            let errMsg = err.message || 'Koneksi terputus.';
            if (err.name === 'AbortError') {
                errMsg = 'Waktu permintaan habis (timeout). Silakan periksa koneksi internet Anda.';
            }

            Swal.fire({
                icon: 'error',
                title: 'Gagal Mengirim',
                text: errMsg
            });

            submitBtn.disabled = false;
            submitText.innerText = 'Kirim Laporan & Generate Grid WA';
            submitIcon.className = 'fa-solid fa-paper-plane';
        });
    }
</script>
@endpush
