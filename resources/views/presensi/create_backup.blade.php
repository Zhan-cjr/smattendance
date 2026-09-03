@extends('layouts.mobile.app')
@section('content')
    {{-- <style>
        :root {
            --bg-body: #dff9fb;
            --bg-nav: #ffffff;
            --color-nav: #32745e;
            --color-nav-active: #58907D;
            --bg-indicator: #32745e;
            --color-nav-hover: #3ab58c;
        }
    </style> --}}
    <style>
        /* CSS Variables untuk memudahkan penyesuaian */
        :root {
            --header-height: 60px;
            --info-section-height: 70px;
            --action-section-height: 65px;
            --bottomnav-height: 75px;
            --padding-total: 20px;
            /* Padding dikurangi agar button hampir menyentuh bottomnav */
        }

        /* Tambahan agar kamera portrait dan rounded di semua device */
        .webcam-capture {
            width: 100%;
            max-width: 98vw;
            /* Tinggi dinamis: viewport height dikurangi semua elemen lain */
            /* Formula menggunakan CSS variables untuk fleksibilitas */
            height: calc(100vh - var(--header-height) - var(--info-section-height) - var(--action-section-height) - var(--bottomnav-height) - var(--padding-total));
            min-height: 180px;
            max-height: 400px;
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
        }

        .webcam-capture video,
        .webcam-capture canvas {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            /* Pastikan proporsional */
            border-radius: 24px !important;
            display: block;
            /* background: #222; */
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
            /* Menghapus margin */
            padding: 0 !important;
            /* Menghapus padding */
            width: 100% !important;
            /* Memastikan lebar penuh */
        }

        /* Tambahkan style untuk indikator loading maps */
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

        /* Perbaikan untuk posisi content-section */
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
            padding-bottom: 0 !important;
            /* Padding dihapus karena tinggi kamera sudah dihitung dengan calc() */
            position: relative;
            z-index: 1;
            overflow: visible;
            /* Ubah ke visible agar konten tidak terpotong */
        }

        /* Style untuk tombol scan */
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

        /* Tambahkan arrow icon kustom */
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

        /* Style untuk jam digital */
        .jam-digital-malasngoding {
            background-color: rgba(39, 39, 39, 0.7);
            position: absolute;
            top: 65px;
            /* Di bawah header */
            right: 15px;
            /* Menambah margin kanan */
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

        /* Style modern untuk box deteksi wajah */
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

        /* Modern Presensi Content Wrapper */
        .presensi-content-modern {
            background: linear-gradient(135deg, #e0f7fa 0%, #fff 100%);
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
            padding: 15px 10px 15px 10px;
            /* Padding dikurangi agar lebih kompak dan button lebih dekat ke bottomnav */
            margin: 10px 0;
            margin-bottom: 10px;
            /* Margin bottom dikurangi agar button hampir menyentuh bottomnav */
        }

        .presensi-content-modern,
        .presensi-content-modern * {
            font-family: 'Poppins', sans-serif !important;
        }

        .camera-section {
            padding: 2px;
            position: relative;
        }

        .info-section {
            background: transparent;
            border-radius: 12px;
            padding: 8px 10px;
            /* Padding dikurangi agar lebih kompak */
            backdrop-filter: blur(6px);
            color: #222;
            font-size: 15px;
            display: flex;
            flex-direction: column;
            gap: 4px;
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
            box-shadow: 0 1px 4px rgba(44, 62, 80, 0.12);
        }

        /* Tambahan style jadwal kerja modern */
        .jadwalkerja-row {
            background: linear-gradient(90deg, var(--color-nav) 0%, var(--color-nav-active) 100%);
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
            padding: 0 0px;
        }

        .jadwalkerja-icon {
            font-size: 28px;
            color: #FFD600;
            margin-bottom: -10px;
        }

        .jadwalkerja-label {
            font-size: 13px;
            color: #fff;
            margin-bottom: -8px;
        }

        .jadwalkerja-value {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            letter-spacing: 1px;
        }

        /* Modern absolute tanggal & jam di kamera */
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

        /* Style absolute untuk maps agar menempel di bawah kamera dan di atas listcabang */
        .map-absolute-section {
            position: absolute;
            bottom: 60px;
            width: 92%;
            /* Setelah kamera */
            z-index: 15;
            width: 100%;
            padding: 0 0 10px 0;
            display: flex;
            justify-content: center;
        }

        .map-absolute-section #map {
            height: 120px;
            width: 80%;
            margin: 0 auto;
            opacity: 0.45;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
        }

        /* Responsive untuk berbagai resolusi layar */
        /* Layar kecil (mobile portrait) - tinggi layar ≤ 667px */
        @media screen and (max-height: 667px) {
            .webcam-capture {
                /* Untuk layar kecil, kurangi padding agar kamera lebih besar */
                height: calc(100vh - 270px);
                min-height: 160px;
                max-height: 300px;
            }

            .presensi-content-modern {
                margin-bottom: 10px;
            }
        }

        /* Layar sedang (mobile landscape / tablet portrait) - tinggi 668px - 900px */
        @media screen and (min-height: 668px) and (max-height: 900px) {
            .webcam-capture {
                /* Untuk layar sedang, tinggi lebih besar dengan padding minimal */
                height: calc(100vh - 290px);
                min-height: 200px;
                max-height: 380px;
            }

            .presensi-content-modern {
                margin-bottom: 10px;
            }
        }

        /* Layar besar (tablet landscape / desktop) - tinggi ≥ 901px */
        @media screen and (min-height: 901px) {
            .webcam-capture {
                /* Untuk layar besar, tinggi maksimal dengan padding minimal */
                height: calc(100vh - 310px);
                min-height: 240px;
                max-height: 480px;
            }

            .presensi-content-modern {
                margin-bottom: 10px;
            }
        }

        /* Landscape orientation - tinggi layar ≤ 500px */
        @media screen and (orientation: landscape) and (max-height: 500px) {
            .webcam-capture {
                /* Untuk landscape, kurangi padding agar kamera lebih besar */
                height: calc(100vh - 250px);
                min-height: 140px;
                max-height: 220px;
            }

            .presensi-content-modern {
                margin-bottom: 10px;
            }
        }

        /* Pastikan action-section tidak tertutup bottomnav */
        .action-section {
            margin-bottom: 5px;
            padding-bottom: 0;
        }

        /* SKELETON LOADING STYLES */
        .content-hide {
            display: none !important;
        }

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s ease-in-out infinite;
            border-radius: 8px;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .skeleton-camera {
            width: 100%;
            /* Approximating the calc height for skeleton */
            height: calc(100vh - 270px); 
            min-height: 200px;
            border-radius: 24px;
            margin-bottom: 15px;
        }

        .skeleton-text {
            height: 16px;
            margin-bottom: 8px;
            border-radius: 4px;
        }

        .skeleton-block {
            display: block;
        }

        .skeleton-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .skeleton-col {
            flex: 1;
            height: 60px;
            border-radius: 12px;
        }

        .skeleton-btn {
            height: 45px;
            border-radius: 22px;
            flex: 1;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <!-- Import Google Fonts: Poppins -->
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
        <!-- SKELETON LOADER -->
        <div id="skeleton-loader" class="presensi-content-modern" style="background: transparent; box-shadow: none;">
            <!-- Camera Skeleton -->
            <div class="skeleton skeleton-camera"></div>
            
            <!-- Info Skeleton -->
            <div class="info-section" style="background: white; margin-bottom: 15px;">
                <div class="skeleton-row">
                    <div class="skeleton skeleton-col"></div>
                    <div class="skeleton skeleton-col"></div>
                    <div class="skeleton skeleton-col"></div>
                </div>
            </div>

            <!-- Button Skeleton -->
            <div class="action-section">
                <div class="skeleton skeleton-btn"></div>
                <div class="skeleton skeleton-btn"></div>
            </div>
        </div>

        <div id="real-content" class="presensi-content-modern content-hide">
            <div class="camera-section" style="position:relative;">
                <div class="row" style="margin-top: 0;">
                    <div class="col" id="facedetection" style="position:relative;">
                        <!-- Absolute Tanggal & Jam -->
                        <div class="abs-tanggal-modern">{{ DateToIndo(date('Y-m-d')) }}</div>
                        <div class="abs-jam-modern"><span id="jam"></span></div>
                        <div class="webcam-capture"></div>
                        <!-- MAPS ABSOLUTE -->
                        <div class="map-absolute-section">
                            <div id="map">
                                <div id="map-loading">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <div class="mt-2">Memuat peta...</div>
                                </div>
                            </div>
                        </div>
                        @if ($general_setting->multi_lokasi)
                            <div id="listcabang">
                                <div class="select-wrapper">
                                    <select name="cabang" id="cabang" class="form-control">
                                        @foreach ($cabang as $item)
                                            <option {{ $item->kode_cabang == $karyawan->kode_cabang ? 'selected' : '' }}
                                                value="{{ $item->lokasi_cabang }}">
                                                {{ $item->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <!-- Info jam digital dipindah ke info-section -->
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
            <!-- <div class="map-section"> ... </div> -->
            <div class="action-section">
                <button class="btn btn-success bg-primary scan-button" id="absenmasuk" statuspresensi="masuk">
                    <ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon>
                    <span style="font-size:14px">Masuk</span>
                </button>
                <button class="btn btn-danger scan-button" id="absenpulang" statuspresensi="pulang">
                    <ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon>
                    <span style="font-size:14px">Pulang</span>
                </button>
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


    <!--Pulang-->
    <audio id="notifikasi_sudahabsenpulang">
        <source src="{{ asset('assets/sound/sudahabsenpulang.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_absenpulang">
        <source src="{{ asset('assets/sound/absenpulang.mp3') }}" type="audio/mpeg">
    </audio>
@endsection
@push('myscript')
    <!-- Face Recognition dengan Caching -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script src="{{ asset('assets/external/js/face-model-cache.js') }}"></script>
    <script type="text/javascript">
        // Fungsi yang dijalankan ketika halaman selesai dimuat
        // Menggunakan DOMContentLoaded untuk memastikan DOM sudah siap
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                jam();
            });
        } else {
            // DOM sudah dimuat, langsung panggil jam()
            jam();
        }

        // Fungsi untuk menampilkan waktu secara real-time
        function jam() {
            // Mengambil elemen HTML dengan id 'jam'
            var e = document.getElementById('jam');

            // Cek apakah elemen ada sebelum mengatur innerHTML
            if (!e) {
                // Jika elemen belum tersedia, coba lagi setelah 100ms
                setTimeout(jam, 100);
                return;
            }

            // Membuat objek Date untuk mendapatkan waktu saat ini
            var d = new Date(),
                // Variabel untuk menampung jam, menit, dan detik
                h, m, s;
            // Mengambil jam dari objek Date
            h = d.getHours();
            // Mengambil menit dari objek Date dan menambahkan '0' di depan jika kurang dari 10
            m = set(d.getMinutes());
            // Mengambil detik dari objek Date dan menambahkan '0' di depan jika kurang dari 10
            s = set(d.getSeconds());

            // Menampilkan waktu dalam format HH:MM:SS
            e.innerHTML = h + ':' + m + ':' + s;

            // Mengatur waktu untuk memanggil fungsi jam() lagi setelah 1 detik
            setTimeout(jam, 1000);
        }

        // Fungsi untuk menambahkan '0' di depan angka jika kurang dari 10
        function set(e) {
            // Jika angka kurang dari 10, tambahkan '0' di depan
            e = e < 10 ? '0' + e : e;
            // Mengembalikan angka yang telah ditambahkan '0' di depan jika perlu
            return e;
        }
    </script>
    <script>
        // Fungsi yang dijalankan ketika dokumen siap
        $(function() {
            // Variabel untuk menampung lokasi
            let lokasi;
            // Variabel untuk menampung lokasi user
            let lokasi_user;
            let multi_lokasi = {{ $general_setting->multi_lokasi }};
            let lokasi_cabang = multi_lokasi ? document.getElementById('cabang').value :
                "{{ $lokasi_kantor->lokasi_cabang }}";
            // Variabel map global & pengecekan izin
            let map;
            let cameraPermissionGranted = false;
            let locationPermissionGranted = false;
            let cameraPermissionDenied = false;
            let locationPermissionDenied = false;
            let cameraPermissionAlertShown = false;
            let locationPermissionAlertShown = false;
            // alert(lokasi_cabang);
            // Mengambil elemen HTML dengan id 'notifikasi_radius'
            let notifikasi_radius = document.getElementById('notifikasi_radius');
            // Mengambil elemen HTML dengan id 'notifikasi_mulaiabsen'
            let notifikasi_mulaiabsen = document.getElementById('notifikasi_mulaiabsen');
            // Mengambil elemen HTML dengan id 'notifikasi_akhirabsen'
            let notifikasi_akhirabsen = document.getElementById('notifikasi_akhirabsen');
            // Mengambil elemen HTML dengan id 'notifikasi_sudahabsen'
            let notifikasi_sudahabsen = document.getElementById('notifikasi_sudahabsen');
            // Mengambil elemen HTML dengan id 'notifikasi_absenmasuk'
            let notifikasi_absenmasuk = document.getElementById('notifikasi_absenmasuk');

            // Mengambil elemen HTML dengan id 'notifikasi_sudahabsenpulang'
            let notifikasi_sudahabsenpulang = document.getElementById('notifikasi_sudahabsenpulang');
            // Mengambil elemen HTML dengan id 'notifikasi_absenpulang'
            let notifikasi_absenpulang = document.getElementById('notifikasi_absenpulang');

            // Variabel untuk menampung status face recognition
            let faceRecognitionDetected = 0; // Inisialisasi variabel face recognition detected
            // Mengambil nilai face recognition dari variabel $general_setting->face_recognition
            let faceRecognition = "{{ $general_setting->face_recognition }}";

            // --- Tambahkan deteksi device mobile di awal script ---
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            console.log(isMobile);
            // Fungsi untuk inisialisasi webcam
                // ===============================================
                // WEBCAM & FACE RECOGNITION MODERN SYSTEM
                // ===============================================
                // Logika webcam dan face recognition telah dipindahkan 
                // ke blok modular di bagian bawah script ini.
                // ===============================================


            // SKELETON LOADING LOGIC moved to App.init() for better synchronization


            // Map initialization moved to App.init() for robustness

            // Fungsi untuk memuat peta

            // Fungsi yang dijalankan ketika geolocation berhasil
            function successCallback(position) {
                locationPermissionGranted = true;
                locationPermissionDenied = false;
                locationPermissionAlertShown = false;
                
                // === ROBUSTNESS FIX: Check if map container exists ===
                const mapContainer = document.getElementById('map');
                if (!mapContainer) {
                    console.error("Map container '#map' not found in DOM.");
                    return;
                }

                try {
                    // Initialize Leaflet map
                    map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);
                    
                    var lokasi_kantor = lokasi_cabang;
                    lokasi = position.coords.latitude + "," + position.coords.longitude;
                    var lok = lokasi_kantor.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];
                    var radius = "{{ $lokasi_kantor->radius_cabang }}";

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
                    var circle = L.circle([lat_kantor, long_kantor], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: radius
                    }).addTo(map);

                    // Sembunyikan indikator loading (with null check)
                    const loader = document.getElementById('map-loading');
                    if (loader) loader.style.display = 'none';

                    setTimeout(function() {
                        if (map) map.invalidateSize();
                    }, 500);
                } catch (error) {
                    console.error("Error initializing map:", error);
                    const loader = document.getElementById('map-loading');
                    if (loader) loader.style.display = 'none';
                }
            }

            // Fungsi yang dijalankan ketika geolocation gagal
            function errorCallback(error) {
                console.error("Error getting geolocation:", error);
                
                const loader = document.getElementById('map-loading');
                if (loader) {
                    loader.innerHTML = 'Gagal mendapatkan lokasi. Silakan cek izin lokasi.';
                }

                locationPermissionGranted = false;
                if (!locationPermissionAlertShown && error && error.code === error.PERMISSION_DENIED) {
                    locationPermissionAlertShown = true;
                    locationPermissionDenied = true;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Izin Lokasi Dibutuhkan',
                        text: 'Akses lokasi diperlukan untuk proses presensi. Mohon izinkan lokasi pada browser/perangkat Anda.'
                    });
                }

                // Coba inisialisasi peta dengan lokasi cabang default
                try {
                    const mapContainer = document.getElementById('map');
                    if (!mapContainer) return;

                    var lok = lokasi_cabang.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];

                    map = L.map('map').setView([lat_kantor, long_kantor], 18);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    var radius = "{{ $lokasi_kantor->radius_cabang }}";
                    var circle = L.circle([lat_kantor, long_kantor], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: radius
                    }).addTo(map);

                    if (loader) loader.style.display = 'none';
                } catch (mapError) {
                    console.error("Error initializing fallback map:", mapError);
                }
            }

            // =========================================================
            // MODERN FACE RECOGNITION IMPLEMENTATION
            // =========================================================

            /**
             * Configuration Module
             */
            const FaceConfig = {
                isEnabled: {{ $general_setting->face_recognition }},
                modelsUrl: '/models',
                detection: {
                    // MOBILE OPTIMIZATION: increased interval for less CPU usage
                    interval: isMobile ? 200 : 80, 
                    // MOBILE OPTIMIZATION: reduced inputSize from 416 to 224 for faster processing
                    inputSize: isMobile ? 224 : 416, 
                    scoreThreshold: 0.3, 
                    minConfidence: 0.45,
                    // MOBILE OPTIMIZATION: quick detection for faster response
                    minStableFrames: isMobile ? 1 : 2, 
                    maxNoFaceFrames: isMobile ? 3 : 4,
                    mirror: false,
                    // MOBILE OPTIMIZATION: skip brightness normalization on mobile (CPU intensive)
                    skipBrightnessNormalization: isMobile
                },
                retry: {
                    maxAttempts: 10,
                    backoffFactor: 1.5
                },
                user: {
                    nik: "{{ $karyawan->nik }}",
                    name: "{{ getNamaDepan(strtolower($karyawan->nama_karyawan)) }}",
                    fullName: "{{ $karyawan->nama_karyawan }}",
                    wajahCount: parseInt("{{ $wajah }}") || 0
                }
            };

            /**
             * UI Controller Module
             */
            const UI = {
                els: {
                    container: document.getElementById('facedetection'),
                    video: null, // Will be set after camera init
                    canvas: null,
                    absenButtons: [document.getElementById('absenmasuk'), document.getElementById('absenpulang')]
                },
                
                showLoading(id, message) {
                    this.removeLoading(id);
                    const el = document.getElementById('facedetection');
                    if(!el) return;

                    const loader = document.createElement('div');
                    loader.id = id;
                    loader.className = 'loading-overlay';
                    loader.innerHTML = `
                        <div class="spinner-border text-light" role="status"></div>
                        <div class="mt-2 text-light">${message}</div>
                    `;
                    loader.style.position = 'absolute';
                    loader.style.top = '50%';
                    loader.style.left = '50%';
                    loader.style.transform = 'translate(-50%, -50%)';
                    loader.style.zIndex = '1000';
                    loader.style.textAlign = 'center';
                    
                    el.appendChild(loader);
                },

                removeLoading(id) {
                    const el = document.getElementById(id);
                    if (el) el.remove();
                },

                showError(message, isFatal = false) {
                    const el = document.getElementById('facedetection');
                    if(!el) return;

                    // Remove existing errors
                    const existing = el.querySelector('.alert-error-message');
                    if(existing) existing.remove();

                    if(message) {
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'alert-error-message';
                        errorMsg.innerHTML = `<div class="alert alert-danger">${message}</div>`;
                        errorMsg.style.position = 'absolute';
                        errorMsg.style.bottom = '10px';
                        errorMsg.style.width = '100%';
                        errorMsg.style.zIndex = '2000';
                        errorMsg.style.textAlign = 'center';
                        el.appendChild(errorMsg);
                    }
                    
                    if(isFatal) this.disableButtons();
                },

                disableButtons() {
                   if(this.els.absenButtons) {
                        this.els.absenButtons.forEach(btn => {
                            if(btn) btn.disabled = true;
                        });
                   }
                },

                enableButtons() {
                    if(this.els.absenButtons) {
                        this.els.absenButtons.forEach(btn => {
                            if(btn) btn.disabled = false;
                        });
                    }
                }
            };

            /**
             * Image Processing Helpers
             */
            const ImageProcessing = {
                /**
                 * Normalize brightness of an image element
                 * @param {HTMLImageElement|HTMLVideoElement|HTMLCanvasElement} img - Image/Video element to normalize
                 * @param {number} targetMean - Target mean brightness (default: 128)
                 * @returns {HTMLCanvasElement|HTMLImageElement} - Canvas with normalized image or original if invalid
                 */
                normalizeBrightness(img, targetMean = 128) {
                    try {
                        // Validate input dimensions
                        const width = img.width || img.videoWidth || 0;
                        const height = img.height || img.videoHeight || 0;
                        
                        if (width === 0 || height === 0) {
                            console.warn('Cannot normalize brightness: invalid dimensions', width, 'x', height);
                            return img; // Return original if dimensions invalid
                        }
                        
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        
                        canvas.width = width;
                        canvas.height = height;
                        
                        // Draw original image
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Get image data
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const data = imageData.data;
                        
                        // Calculate current mean brightness
                        let sum = 0;
                        for (let i = 0; i < data.length; i += 4) {
                            sum += (data[i] + data[i + 1] + data[i + 2]) / 3;
                        }
                        const currentMean = sum / (data.length / 4);
                        
                        // Skip normalization if mean is already close to target
                        if (Math.abs(currentMean - targetMean) < 5) {
                            console.log(`Brightness already optimal: ${currentMean.toFixed(1)}`);
                            return canvas;
                        }
                        
                        // Calculate adjustment factor
                        const adjustment = targetMean / currentMean;
                        
                        // Apply brightness normalization
                        for (let i = 0; i < data.length; i += 4) {
                            data[i] = Math.min(255, data[i] * adjustment);     // R
                            data[i + 1] = Math.min(255, data[i + 1] * adjustment); // G
                            data[i + 2] = Math.min(255, data[i + 2] * adjustment); // B
                            // Alpha channel (i + 3) unchanged
                        }
                        
                        // Put normalized data back
                        ctx.putImageData(imageData, 0, 0);
                        
                        console.log(`Brightness normalized: ${currentMean.toFixed(1)} → ${targetMean}`);
                        
                        return canvas;
                    } catch (error) {
                        console.error('Error in brightness normalization:', error);
                        return img; // Return original on error
                    }
                }
            };

            /**
             * Camera Controller Module
             */
            const Camera = {
                init() {
                    return new Promise((resolve, reject) => {
                        Webcam.set({
                            height: 480,
                            width: 640,
                            image_format: 'jpeg',
                            jpeg_quality: 95, // Standardized to 95% for all devices
                            fps: isMobile ? 15 : 30,
                            constraints: {
                                video: {
                                    width: { ideal: 640 }, // Standardized to 640 for all
                                    height: { ideal: 480 }, // Standardized to 480 for all
                                    facingMode: "user",
                                    frameRate: { ideal: isMobile ? 15 : 30 }
                                }
                            }
                        });

                        Webcam.attach('.webcam-capture');

                        Webcam.on('load', () => {
                            console.log('Camera loaded');
                            cameraPermissionGranted = true;
                            
                            // === FIX: Retry mechanism for video element ===
                            let retryCount = 0;
                            const maxRetries = 10;
                            
                            const findVideo = () => {
                                const video = document.querySelector('.webcam-capture video');
                                if(video) {
                                    console.log('Video element found');
                                    UI.els.video = video;
                                    resolve(video);
                                } else if (retryCount < maxRetries) {
                                    retryCount++;
                                    console.log(`Retrying to find video element... (${retryCount}/${maxRetries})`);
                                    setTimeout(findVideo, 100); // Wait 100ms and retry
                                } else {
                                    reject(new Error('Video element not found after retries'));
                                }
                            };
                            
                            // Start finding video element
                            setTimeout(findVideo, 100); // Initial slight delay
                        });

                        Webcam.on('error', (err) => {
                            console.error('Camera error', err);
                            const errName = (err?.name || '').toLowerCase();
                            if (errName.includes('notallowed') || errName.includes('permission')) {
                                cameraPermissionDenied = true;
                                UI.showError('Izin kamera ditolak. Mohon izinkan akses kamera.', true);
                            }
                            reject(err);
                        });
                    });
                }
            };

            /**
             * Face Service Module
             */
            const FaceService = {
                modelsLoaded: false,
                descriptors: null,
                matcher: null,
                isDetecting: false,
                
                async loadModels() {
                    UI.showLoading('model-loading', 'Memuat model wajah...');
                    
                    try {
                        const modelPath = FaceConfig.modelsUrl;
                        if (isMobile) {
                            await Promise.all([
                                faceapi.nets.tinyFaceDetector.loadFromUri(modelPath),
                                faceapi.nets.faceLandmark68Net.loadFromUri(modelPath),
                                faceapi.nets.faceRecognitionNet.loadFromUri(modelPath)
                            ]);
                        } else {
                            await Promise.all([
                                faceapi.nets.ssdMobilenetv1.loadFromUri(modelPath),
                                faceapi.nets.faceLandmark68Net.loadFromUri(modelPath),
                                faceapi.nets.faceRecognitionNet.loadFromUri(modelPath)
                            ]);
                        }
                        this.modelsLoaded = true;
                        UI.removeLoading('model-loading');
                        console.log('Models loaded');
                    } catch (e) {
                        console.error('Model load failed', e);
                        UI.showError('Gagal memuat model wajah.');
                        throw e;
                    }
                },

                async loadDescriptors() {
                    UI.showLoading('data-loading', 'Memuat data wajah...');
                    
                    try {
                        const nik = FaceConfig.user.nik;
                        let descriptions = [];
                        
                        // === FIX: Always fetch from SERVER first to detect changes ===
                        // Cache is used only as fallback if server fetch fails
                        let serverFetchSuccess = false;
                        
                        try {
                            const timestamp = new Date().getTime();
                            const response = await fetch(`/facerecognition/getwajah?t=${timestamp}`);
                            const data = await response.json();
                            
                            if (data && data.length > 0) {
                                const label = `${FaceConfig.user.nik}-${FaceConfig.user.name}`;
                                
                                // Process images parallel
                                const promises = data.map(async (faceData) => {
                                    try {
                                        const imgPath = `/storage/uploads/facerecognition/${label}/${faceData.wajah}`;
                                        const img = await faceapi.fetchImage(imgPath);
                                        
                                        // === BRIGHTNESS NORMALIZATION ===
                                        // Normalize brightness before generating descriptor
                                        // This helps match photos with different lighting/exposure
                                        const normalizedCanvas = ImageProcessing.normalizeBrightness(img, 128);
                                        
                                        const options = isMobile 
                                            ? new faceapi.TinyFaceDetectorOptions({ inputSize: 224 })
                                            : new faceapi.SsdMobilenetv1Options({ minConfidence: 0.4 });

                                        // Use normalized image for detection
                                        const detection = await faceapi.detectSingleFace(normalizedCanvas, options)
                                            .withFaceLandmarks()
                                            .withFaceDescriptor();

                                        return detection ? detection.descriptor : null;
                                    } catch (err) {
                                        console.warn('Error processing face image', err);
                                        return null;
                                    }
                                });

                                const results = await Promise.all(promises);
                                descriptions = results.filter(d => d !== null);

                                // Update cache with fresh data
                                if (descriptions.length > 0 && window.FaceModelCache) {
                                    await window.FaceModelCache.saveDescriptors(nik, descriptions, data.map(d=>d.wajah));
                                    console.log('Face data loaded from server and cached');
                                } else if (window.FaceModelCache) {
                                    // Clear cache if no data from server (user deleted their photos)
                                    await window.FaceModelCache.clearDescriptors(nik);
                                    console.log('No face data on server, cache cleared');
                                }
                                
                                serverFetchSuccess = true;
                            } else {
                                // No data from server - clear cache
                                if (window.FaceModelCache) {
                                    await window.FaceModelCache.clearDescriptors(nik);
                                }
                                console.log('No face data from server');
                            }
                        } catch (serverError) {
                            console.warn('Failed to fetch from server, trying cache...', serverError);
                            
                            // Fallback to cache only if server fetch fails
                            if (window.FaceModelCache && window.FaceModelCache.loadDescriptors) {
                                const cached = await window.FaceModelCache.loadDescriptors(nik);
                                if (cached && cached.descriptors.length > 0) {
                                    console.log('Using cached descriptors (server unavailable)');
                                    descriptions = cached.descriptors;
                                }
                            }
                        }

                        if (descriptions.length === 0) {
                             console.warn('No face data available');
                        }

                        if(descriptions.length > 0) {
                            const labelName = FaceConfig.user.fullName;
                            const labeledDescriptors = new faceapi.LabeledFaceDescriptors(labelName, descriptions);
                            // Threshold 0.55: Balanced between security and tolerance
                            // Lower = stricter, Higher = more tolerant
                            this.matcher = new faceapi.FaceMatcher(labeledDescriptors, 0.55);
                        } else {
                            this.matcher = null; 
                        }
                        
                        UI.removeLoading('data-loading');
                        console.log('Descriptors loaded');
                    } catch (e) {
                        console.error('Descriptor load failed', e);
                        UI.showError('Gagal memuat data wajah.');
                        UI.removeLoading('data-loading');
                        throw e;
                    }
                },

                startDetection(video) {
                    if(this.isDetecting) return;
                    this.isDetecting = true;
                    
                    // === CRITICAL FIX: Match Canvas to DISPLAYED Video Size ===
                    // The video element is scaled by CSS (object-fit: cover)
                    // We need to match the canvas to the DISPLAYED size, not intrinsic size
                    const canvas = faceapi.createCanvasFromMedia(video);
                    UI.els.container.appendChild(canvas);
                    UI.els.canvas = canvas;
                    
                    // Get displayed dimensions (accounting for CSS scaling)
                    const rect = video.getBoundingClientRect();
                    const displaySize = { width: Math.round(rect.width), height: Math.round(rect.height) };
                    
                    // Set canvas to match EXACTLY the displayed video size
                    canvas.width = displaySize.width;
                    canvas.height = displaySize.height;
                    canvas.style.width = displaySize.width + 'px';
                    canvas.style.height = displaySize.height + 'px';
                    canvas.style.position = 'absolute';
                    canvas.style.top = '0';
                    canvas.style.left = '0';
                    canvas.style.objectFit = 'none'; // Critical: no scaling on canvas
                    
                    console.log('Video intrinsic:', video.videoWidth, 'x', video.videoHeight);
                    console.log('Video displayed:', displaySize.width, 'x', displaySize.height);

                    let stableCount = 0;
                    let noFaceCount = 0;
                    let isProcessing = false;
                    
                    const loop = async () => {
                        if(!this.isDetecting) return;

                        if(!isProcessing) {
                            isProcessing = true;
                            try {
                                // === BRIGHTNESS NORMALIZATION (SKIP ON MOBILE FOR PERFORMANCE) ===
                                // On mobile, skip brightness normalization as it's too CPU intensive
                                const inputSource = FaceConfig.detection.skipBrightnessNormalization 
                                    ? video 
                                    : ImageProcessing.normalizeBrightness(video, 128);
                                
                                const options = isMobile 
                                    ? new faceapi.TinyFaceDetectorOptions({ 
                                        inputSize: FaceConfig.detection.inputSize, 
                                        scoreThreshold: FaceConfig.detection.scoreThreshold 
                                    })
                                    : new faceapi.SsdMobilenetv1Options({ 
                                        minConfidence: FaceConfig.detection.minConfidence 
                                    });

                                // Use inputSource (video or normalized canvas) for detection
                                let detection = await faceapi.detectSingleFace(inputSource, options)
                                    .withFaceLandmarks()
                                    .withFaceDescriptor();

                                const ctx = canvas.getContext('2d');
                                ctx.clearRect(0, 0, canvas.width, canvas.height);

                                if (detection) {
                                    noFaceCount = 0;
                                    stableCount++;

                                    if(stableCount >= FaceConfig.detection.minStableFrames) {
                                        // === FIX: Scale detection from VIDEO size to DISPLAY size ===
                                        // Critical for mobile: video intrinsic size != displayed size
                                        const videoSize = { width: video.videoWidth, height: video.videoHeight };
                                        const displaySize = { width: canvas.width, height: canvas.height };
                                        
                                        // Resize detection results to match displayed canvas
                                        const resizedDetections = faceapi.resizeResults(detection, displaySize);
                                        
                                        let match = null;
                                        let isRecognized = false;
                                        let labelText = 'Wajah Tidak Dikenali';
                                        
                                        if(this.matcher) {
                                            // Use ORIGINAL descriptor (not resized) for matching
                                            match = this.matcher.findBestMatch(detection.descriptor);
                                            isRecognized = match.label !== 'unknown';
                                            
                                            // Add score to label text for easy debugging
                                            if (isRecognized) {
                                                labelText = `${match.label} (${match.distance.toFixed(2)})`;
                                            } else {
                                                labelText = `Wajah Tidak Dikenali (${match.distance.toFixed(2)})`;
                                            }
                                            
                                            // === LOG SIMILARITY SCORE FOR DEBUGGING ===
                                            console.log('=== Face Recognition Score ===');
                                            console.log('Label:', match.label);
                                            console.log('Distance (Similarity Score):', match.distance.toFixed(4));
                                            console.log('Threshold:', '0.55 (lower is better match)');
                                            console.log('Recognized:', isRecognized);
                                            console.log('Explanation:', match.distance < 0.55 ? 'Match found (distance < 0.55)' : 'No match (distance >= 0.55)');
                                            console.log('Video size:', videoSize.width, 'x', videoSize.height);
                                            console.log('Display size:', displaySize.width, 'x', displaySize.height);
                                            console.log('============================');
                                        }
                                        
                                        // Update Global State
                                        faceRecognitionDetected = isRecognized ? 1 : 0;
                                        
                                        // === MODERN ROUNDED BOX DESIGN ===
                                        // Use RESIZED detection box for drawing
                                        let box = resizedDetections.detection.box;
                                        
                                        // === MAKE BOX SQUARE ===
                                        const size = Math.max(box.width, box.height);
                                        const squareBox = {
                                            x: box.x + (box.width - size) / 2,
                                            y: box.y + (box.height - size) / 2,
                                            width: size,
                                            height: size
                                        };
                                        box = squareBox;
                                        
                                        const color = isRecognized ? '#4CAF50' : '#FFC107';
                                        const ctx = canvas.getContext('2d');
                                        
                                        // Draw rounded rectangle with glow
                                        const cornerRadius = 16;
                                        const lineWidth = 4;
                                        
                                        // Shadow/Glow effect
                                        ctx.save();
                                        ctx.shadowColor = color;
                                        ctx.shadowBlur = 20;
                                        ctx.shadowOffsetX = 0;
                                        ctx.shadowOffsetY = 0;
                                        
                                        // Draw rounded box
                                        ctx.strokeStyle = color;
                                        ctx.lineWidth = lineWidth;
                                        ctx.lineJoin = 'round';
                                        ctx.lineCap = 'round';
                                        
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + cornerRadius, box.y);
                                        ctx.lineTo(box.x + box.width - cornerRadius, box.y);
                                        ctx.quadraticCurveTo(box.x + box.width, box.y, box.x + box.width, box.y + cornerRadius);
                                        ctx.lineTo(box.x + box.width, box.y + box.height - cornerRadius);
                                        ctx.quadraticCurveTo(box.x + box.width, box.y + box.height, box.x + box.width - cornerRadius, box.y + box.height);
                                        ctx.lineTo(box.x + cornerRadius, box.y + box.height);
                                        ctx.quadraticCurveTo(box.x, box.y + box.height, box.x, box.y + box.height - cornerRadius);
                                        ctx.lineTo(box.x, box.y + cornerRadius);
                                        ctx.quadraticCurveTo(box.x, box.y, box.x + cornerRadius, box.y);
                                        ctx.closePath();
                                        ctx.stroke();
                                        ctx.restore();
                                        
                                        // === SCANNING ANIMATION ===
                                        // Animated scanning line that moves up and down
                                        const scanSpeed = 0.05; // Speed of animation
                                        const scanProgress = (Date.now() * scanSpeed) % (box.height * 2);
                                        const scanY = scanProgress < box.height 
                                            ? box.y + scanProgress 
                                            : box.y + (box.height * 2 - scanProgress);
                                        
                                        ctx.save();
                                        // Create gradient for scan line
                                        const scanGradient = ctx.createLinearGradient(box.x, scanY - 15, box.x, scanY + 15);
                                        scanGradient.addColorStop(0, 'rgba(255, 255, 255, 0)');
                                        scanGradient.addColorStop(0.5, `rgba(255, 255, 255, 0.6)`);
                                        scanGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
                                        
                                        ctx.strokeStyle = scanGradient;
                                        ctx.lineWidth = 3;
                                        ctx.shadowColor = '#FFFFFF';
                                        ctx.shadowBlur = 10;
                                        
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + 10, scanY);
                                        ctx.lineTo(box.x + box.width - 10, scanY);
                                        ctx.stroke();
                                        ctx.restore();
                                        
                                        // Draw corner brackets for extra tech feel
                                        ctx.save();
                                        ctx.strokeStyle = color;
                                        ctx.lineWidth = 3;
                                        const bracketSize = 20;
                                        
                                        // Top-left
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + cornerRadius, box.y);
                                        ctx.lineTo(box.x, box.y);
                                        ctx.lineTo(box.x, box.y + bracketSize);
                                        ctx.stroke();
                                        
                                        // Top-right
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + box.width - cornerRadius, box.y);
                                        ctx.lineTo(box.x + box.width, box.y);
                                        ctx.lineTo(box.x + box.width, box.y + bracketSize);
                                        ctx.stroke();
                                        
                                        // Bottom-left
                                        ctx.beginPath();
                                        ctx.moveTo(box.x, box.y + box.height - bracketSize);
                                        ctx.lineTo(box.x, box.y + box.height);
                                        ctx.lineTo(box.x + cornerRadius, box.y + box.height);
                                        ctx.stroke();
                                        
                                        // Bottom-right
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + box.width, box.y + box.height - bracketSize);
                                        ctx.lineTo(box.x + box.width, box.y + box.height);
                                        ctx.lineTo(box.x + box.width - cornerRadius, box.y + box.height);
                                        ctx.stroke();
                                        ctx.restore();
                                        
                                        // Draw modern label
                                        ctx.save();
                                        ctx.font = 'bold 14px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';
                                        
                                        const labelPadding = 8;
                                        const textMetrics = ctx.measureText(labelText);
                                        const labelWidth = textMetrics.width + labelPadding * 2;
                                        const labelHeight = 28;
                                        const labelX = box.x + box.width / 2 - labelWidth / 2;
                                        const labelY = box.y - labelHeight - 8;
                                        
                                        // Label background with gradient
                                        const gradient = ctx.createLinearGradient(labelX, labelY, labelX, labelY + labelHeight);
                                        gradient.addColorStop(0, color);
                                        gradient.addColorStop(1, color + 'CC'); // Add transparency
                                        
                                        ctx.fillStyle = gradient;
                                        ctx.shadowColor = 'rgba(0,0,0,0.3)';
                                        ctx.shadowBlur = 8;
                                        ctx.shadowOffsetY = 2;
                                        
                                        // Rounded label background
                                        const labelRadius = 6;
                                        ctx.beginPath();
                                        ctx.moveTo(labelX + labelRadius, labelY);
                                        ctx.lineTo(labelX + labelWidth - labelRadius, labelY);
                                        ctx.quadraticCurveTo(labelX + labelWidth, labelY, labelX + labelWidth, labelY + labelRadius);
                                        ctx.lineTo(labelX + labelWidth, labelY + labelHeight - labelRadius);
                                        ctx.quadraticCurveTo(labelX + labelWidth, labelY + labelHeight, labelX + labelWidth - labelRadius, labelY + labelHeight);
                                        ctx.lineTo(labelX + labelRadius, labelY + labelHeight);
                                        ctx.quadraticCurveTo(labelX, labelY + labelHeight, labelX, labelY + labelHeight - labelRadius);
                                        ctx.lineTo(labelX, labelY + labelRadius);
                                        ctx.quadraticCurveTo(labelX, labelY, labelX + labelRadius, labelY);
                                        ctx.closePath();
                                        ctx.fill();
                                        
                                        // Label text
                                        ctx.shadowBlur = 0;
                                        ctx.fillStyle = '#FFFFFF';
                                        ctx.fillText(labelText, labelX + labelWidth / 2, labelY + labelHeight / 2);
                                        ctx.restore();

                                        if (isRecognized) {
                                            UI.enableButtons();
                                        } else {
                                            UI.disableButtons();
                                        }
                                    }
                                } else {
                                    noFaceCount++;
                                    if(noFaceCount > FaceConfig.detection.maxNoFaceFrames) {
                                        stableCount = 0;
                                        faceRecognitionDetected = 0;
                                        UI.disableButtons();
                                        
                                        // === DRAW NO FACE DETECTED ALERT ===
                                        const ctx = canvas.getContext('2d');
                                        
                                        // Alert box dimensions
                                        const alertWidth = 280;
                                        const alertHeight = 80;
                                        const alertX = (canvas.width - alertWidth) / 2;
                                        const alertY = (canvas.height - alertHeight) / 2;
                                        const alertRadius = 12;
                                        
                                        // Draw alert background with gradient
                                        ctx.save();
                                        const gradient = ctx.createLinearGradient(alertX, alertY, alertX, alertY + alertHeight);
                                        gradient.addColorStop(0, 'rgba(244, 67, 54, 0.95)'); // Red
                                        gradient.addColorStop(1, 'rgba(244, 67, 54, 0.85)');
                                        
                                        ctx.fillStyle = gradient;
                                        ctx.shadowColor = 'rgba(0, 0, 0, 0.4)';
                                        ctx.shadowBlur = 20;
                                        ctx.shadowOffsetY = 4;
                                        
                                        // Rounded rectangle
                                        ctx.beginPath();
                                        ctx.moveTo(alertX + alertRadius, alertY);
                                        ctx.lineTo(alertX + alertWidth - alertRadius, alertY);
                                        ctx.quadraticCurveTo(alertX + alertWidth, alertY, alertX + alertWidth, alertY + alertRadius);
                                        ctx.lineTo(alertX + alertWidth, alertY + alertHeight - alertRadius);
                                        ctx.quadraticCurveTo(alertX + alertWidth, alertY + alertHeight, alertX + alertWidth - alertRadius, alertY + alertHeight);
                                        ctx.lineTo(alertX + alertRadius, alertY + alertHeight);
                                        ctx.quadraticCurveTo(alertX, alertY + alertHeight, alertX, alertY + alertHeight - alertRadius);
                                        ctx.lineTo(alertX, alertY + alertRadius);
                                        ctx.quadraticCurveTo(alertX, alertY, alertX + alertRadius, alertY);
                                        ctx.closePath();
                                        ctx.fill();
                                        
                                        // Draw icon (!) 
                                        ctx.shadowBlur = 0;
                                        ctx.fillStyle = '#FFFFFF';
                                        ctx.font = 'bold 32px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';
                                        ctx.fillText('⚠️', alertX + alertWidth / 2, alertY + 28);
                                        
                                        // Draw text
                                        ctx.font = 'bold 16px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                                        ctx.fillText('Wajah Tidak Terdeteksi', alertX + alertWidth / 2, alertY + 58);
                                        
                                        ctx.restore();
                                    }
                                }
                            } catch (e) {
                                console.warn('Detection loop error', e);
                            }
                            isProcessing = false;
                        }

                        // Throttle with requestAnimationFrame
                        setTimeout(() => requestAnimationFrame(loop), FaceConfig.detection.interval);
                    };

                    loop();
                }
            };

            /**
             * Main Application Orchestrator
             */
            const App = {
                async init() {
                    console.log('Initializing Modern App Logic...');
                    
                    try {
                        const video = await Camera.init();
                        console.log('Camera initialized, revealing UI...');
                        
                        // Reveal UI as soon as camera is ready
                        // We use a small timeout to ensure the DOM has settled
                        setTimeout(() => {
                            $("#skeleton-loader").fadeOut(200, function() {
                                $(this).remove();
                                $("#real-content").removeClass("content-hide").hide().fadeIn(200, async function() {
                                    console.log('UI Revealed, starting functional modules...');
                                    
                                    // 1. Start Face Recognition if enabled
                                    if (FaceConfig.isEnabled == 1) {
                                        UI.disableButtons(); 
                                        try {
                                            await FaceService.loadModels();
                                            await FaceService.loadDescriptors();
                                            FaceService.startDetection(video);
                                        } catch (faceErr) {
                                            console.error('Face Recognition Init Failed', faceErr);
                                            UI.showError('Sistem deteksi wajah gagal dimuat. Anda tetap bisa melakukan presensi.');
                                            UI.enableButtons(); // Fallback
                                        }
                                    } else {
                                        console.log('Face Recognition is disabled, enabling buttons.');
                                        UI.enableButtons();
                                    }
                                    
                                    // 2. Start Map & Geolocation after UI reveal
                                    if (navigator.geolocation) {
                                        console.log('Triggering geolocation for map...');
                                        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
                                    }
                                    
                                    if(map) {
                                        console.log('Invalidating map size for correct rendering');
                                        map.invalidateSize();
                                    }
                                });
                            });
                        }, 100);

                    } catch (e) {
                        console.error('App Init Error', e);
                        // Emergency reveal if initialization fails
                        $("#skeleton-loader").remove();
                        $("#real-content").removeClass("content-hide");
                        UI.showError('Gagal memuat kamera. Silakan refresh halaman.');
                    }
                }
            };

            // Start App
            App.init();

            // [REFRACTOR] LEGACY CODE DISABLED
            // This block is replaced by App.init() above
            if (false) {
                // Preload descriptors di background (non-blocking)
                const nik = "{{ $karyawan->nik }}";
                const label = "{{ $karyawan->nik }}-{{ getNamaDepan(strtolower($karyawan->nama_karyawan)) }}";

                // Preload descriptors jika cache utility tersedia
                if (window.FaceModelCache && typeof window.FaceModelCache.preloadFaceDescriptors === 'function') {
                    // Tunggu face-api.js selesai dimuat, lalu preload di background
                    if (typeof faceapi !== 'undefined') {
                        window.FaceModelCache.preloadFaceDescriptors(nik, label).then(success => {
                            if (success) {
                                console.log(`[Presensi] Descriptors preloaded for ${nik} in background`);
                            }
                        }).catch(err => {
                            console.warn(`[Presensi] Failed to preload descriptors:`, err);
                        });
                    } else {
                        // Tunggu face-api.js dimuat
                        const checkFaceApi = setInterval(() => {
                            if (typeof faceapi !== 'undefined') {
                                clearInterval(checkFaceApi);
                                window.FaceModelCache.preloadFaceDescriptors(nik, label).then(success => {
                                    if (success) {
                                        console.log(`[Presensi] Descriptors preloaded for ${nik} in background`);
                                    }
                                }).catch(err => {
                                    console.warn(`[Presensi] Failed to preload descriptors:`, err);
                                });
                            }
                        }, 100);
                    }
                }

                // Tambahkan indikator loading dengan styling yang lebih baik
                const loadingIndicator = document.createElement('div');
                loadingIndicator.id = 'face-recognition-loading';
                loadingIndicator.innerHTML = `
    <div class="spinner-border text-light" role="status">
        <span class="sr-only">Memuat pengenalan wajah...</span>
    </div>
    <div class="mt-2 text-light">Memuat model pengenalan wajah...</div>
`;
                loadingIndicator.style.position = 'absolute';
                loadingIndicator.style.top = '50%';
                loadingIndicator.style.left = '50%';
                loadingIndicator.style.transform = 'translate(-50%, -50%)';
                loadingIndicator.style.zIndex = '1000';
                loadingIndicator.style.textAlign = 'center';
                
                const faceDetectionEl = document.getElementById('facedetection');
                if (faceDetectionEl) {
                    faceDetectionEl.appendChild(loadingIndicator);
                } else {
                    console.warn('Element #facedetection not found, cannot append loading indicator');
                }

                // Load model dengan caching (menggunakan utility dari dashboard jika ada)
                let modelLoadingPromise;

                // Cek apakah model sudah di-preload dari dashboard
                const modelsPreloaded = sessionStorage.getItem('faceModelsPreloaded') === 'true';
                const preloadTime = sessionStorage.getItem('faceModelsPreloadTime');
                const preloadAge = preloadTime ? Date.now() - parseInt(preloadTime) : null;

                // Jika sudah di-preload dalam 30 menit terakhir, model mungkin masih di memory browser
                if (modelsPreloaded && preloadAge && preloadAge < 30 * 60 * 1000) {
                    console.log('Models may be cached from dashboard preload, loading...');
                }

                if (window.FaceModelCache && typeof window.FaceModelCache.loadModelWithCache === 'function') {
                    // Gunakan cached loading jika utility tersedia
                    console.log('Using cached model loading...');
                    modelLoadingPromise = isMobile ? Promise.all([
                        window.FaceModelCache.loadModelWithCache(faceapi.nets.tinyFaceDetector, '/models'),
                        window.FaceModelCache.loadModelWithCache(faceapi.nets.faceRecognitionNet, '/models'),
                        window.FaceModelCache.loadModelWithCache(faceapi.nets.faceLandmark68Net, '/models'),
                    ]) : Promise.all([
                        window.FaceModelCache.loadModelWithCache(faceapi.nets.ssdMobilenetv1, '/models'),
                        window.FaceModelCache.loadModelWithCache(faceapi.nets.faceRecognitionNet, '/models'),
                        window.FaceModelCache.loadModelWithCache(faceapi.nets.faceLandmark68Net, '/models'),
                    ]);
                } else {
                    // Fallback: load normal jika utility tidak tersedia
                    console.log('Using normal model loading (cache utility not available)...');
                    modelLoadingPromise = isMobile ? Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                        faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                        faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    ]) : Promise.all([
                        faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
                        faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                        faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    ]);
                }

                // Mulai pengenalan wajah setelah model dimuat
                modelLoadingPromise.then(() => {
                    const loadingEl = document.getElementById('face-recognition-loading');
                    if (loadingEl) loadingEl.remove();

                    // Debugging: Periksa video stream sebelum memulai face recognition
                    const video = document.querySelector('.webcam-capture video');
                    if (video) {
                        console.log('Video element found:', video);
                        console.log('Video readyState:', video.readyState);
                        console.log('Video dimensions:', video.videoWidth, 'x', video.videoHeight);
                        console.log('Video paused:', video.paused);
                        console.log('Video srcObject:', video.srcObject);

                        // Tambahkan event listener untuk monitoring video
                        video.addEventListener('loadedmetadata', () => {
                            console.log('Video metadata loaded:', video.videoWidth, 'x', video.videoHeight);
                        });

                        video.addEventListener('canplay', () => {
                            console.log('Video can play');
                        });

                        video.addEventListener('playing', () => {
                            console.log('Video is playing');
                        });

                        video.addEventListener('error', (e) => {
                            console.error('Video error:', e);
                        });
                    }

                    startFaceRecognition();
                }).catch(err => {
                    console.error("Error loading models:", err);
                    const loadingEl = document.getElementById('face-recognition-loading');
                    if (loadingEl) loadingEl.remove();
                    // Coba muat ulang model jika terjadi error
                    setTimeout(() => {
                        console.log('Retrying to load face recognition models');
                        modelLoadingPromise.then(() => {
                            startFaceRecognition();
                        });
                    }, 2000);
                });

                async function getLabeledFaceDescriptions() {
                    // Pastikan model sudah dimuat sebelum memproses foto
                    await ensureModelsLoaded();

                    const labels = [
                        "{{ $karyawan->nik }}-{{ getNamaDepan(strtolower($karyawan->nama_karyawan)) }}"
                    ];
                    const nik = "{{ $karyawan->nik }}";
                    let namakaryawan;
                    let jmlwajah = "{{ $wajah == 0 ? 1 : $wajah }}";

                    // Tambahkan indikator loading untuk memuat data wajah
                    const faceDataLoading = document.createElement('div');
                    faceDataLoading.id = 'face-data-loading';
                    faceDataLoading.innerHTML = `
        <div class="spinner-border text-light" role="status">
            <span class="sr-only">Memuat data wajah...</span>
        </div>
        <div class="mt-2 text-light">Memuat data wajah...</div>
    `;
                    faceDataLoading.style.position = 'absolute';
                    faceDataLoading.style.top = '50%';
                    faceDataLoading.style.left = '50%';
                    faceDataLoading.style.transform = 'translate(-50%, -50%)';
                    faceDataLoading.style.zIndex = '1000';
                    faceDataLoading.style.textAlign = 'center';
                    
                    const faceDetectionEl = document.getElementById('facedetection');
                    if (faceDetectionEl) {
                        faceDetectionEl.appendChild(faceDataLoading);
                    } else {
                         console.warn('Element #facedetection not found, cannot append face data loading');
                    }

                    try {
                        // Cek apakah descriptors sudah di-cache
                        let cachedDescriptors = null;
                        if (window.FaceModelCache && typeof window.FaceModelCache.loadDescriptors === 'function') {
                            cachedDescriptors = await window.FaceModelCache.loadDescriptors(nik);
                        }

                        if (cachedDescriptors && cachedDescriptors.descriptors && cachedDescriptors.descriptors.length > 0) {
                            // Validasi: Cek apakah data wajah masih ada di server sebelum menggunakan cache
                            const timestamp = new Date().getTime();
                            try {
                                const response = await fetch(`/facerecognition/getwajah?t=${timestamp}`);
                                const serverData = await response.json();

                                // Jika tidak ada data di server, clear cache dan regenerate
                                if (!serverData || serverData.length === 0) {
                                    console.log(`[Presensi] No face data on server for ${nik}, clearing cache...`);
                                    if (window.FaceModelCache && typeof window.FaceModelCache.clearDescriptors === 'function') {
                                        await window.FaceModelCache.clearDescriptors(nik);
                                    }
                                    cachedDescriptors = null; // Force regenerate
                                } else {
                                    // Validasi: Bandingkan jumlah file wajah dengan cache
                                    // Jika jumlah berbeda, cache mungkin outdated, regenerate
                                    const serverFileCount = serverData.length;
                                    const cacheFileCount = cachedDescriptors.wajahFiles ? cachedDescriptors.wajahFiles.length : 0;

                                    if (serverFileCount !== cacheFileCount) {
                                        console.log(
                                            `[Presensi] Face count mismatch (server: ${serverFileCount}, cache: ${cacheFileCount}), clearing cache...`
                                        );
                                        if (window.FaceModelCache && typeof window.FaceModelCache.clearDescriptors === 'function') {
                                            await window.FaceModelCache.clearDescriptors(nik);
                                        }
                                        cachedDescriptors = null; // Force regenerate
                                    } else {
                                        // Cache masih valid, gunakan cache
                                        console.log(
                                            `[Presensi] Using cached descriptors for ${nik} (${cachedDescriptors.descriptors.length} descriptors)`
                                        );
                                        const faceDataLoadingEl = document.getElementById('face-data-loading');
                                        if (faceDataLoadingEl) faceDataLoadingEl.remove();

                                        const result = labels.map(label => {
                                            return new faceapi.LabeledFaceDescriptors(label, cachedDescriptors.descriptors);
                                        });

                                        return result;
                                    }
                                }
                            } catch (validationError) {
                                console.warn(`[Presensi] Error validating cache, regenerating:`, validationError);
                                // Jika error validasi, clear cache dan regenerate untuk aman
                                if (window.FaceModelCache && typeof window.FaceModelCache.clearDescriptors === 'function') {
                                    await window.FaceModelCache.clearDescriptors(nik);
                                }
                                cachedDescriptors = null; // Force regenerate
                            }
                        }

                        // Fallback: Generate descriptors secara parallel (jika cache tidak ada)
                        console.log(`[Presensi] Cache not found, generating descriptors in parallel for ${nik}...`);
                        const timestamp = new Date().getTime();
                        const response = await fetch(`/facerecognition/getwajah?t=${timestamp}`);
                        const data = await response.json();
                        console.log('Data wajah yang diterima:', data);

                        const result = await Promise.all(
                            labels.map(async (label) => {
                                const descriptions = [];
                                let validFaceFound = false;
                                const wajahFiles = [];

                                // Proses foto referensi (Maksimal 2 foto terbaru untuk kecepatan)
                                const processPromises = data.slice(0, 2).map(async (faceData) => {
                                    try {
                                        // Gunakan timestamp tetap untuk cache browser, hanya tambahkan versi jika file baru
                                        // Ini memungkinkan browser cache bekerja dengan baik
                                        const imagePath =
                                            `/storage/uploads/facerecognition/${label}/${faceData.wajah}?v=1`;

                                        const imgResponse = await fetch(imagePath, {
                                            cache: 'force-cache'
                                        });
                                        if (!imgResponse.ok) {
                                            console.warn(`File foto wajah ${faceData.wajah} tidak ditemukan`);
                                            return null;
                                        }

                                        const img = await faceapi.fetchImage(imagePath);
                                        if (!img) return null;

                                        // Generate descriptor
                                        let detections;
                                        try {
                                            if (isMobile) {
                                                // Pastikan TinyFaceDetector sudah dimuat
                                                if (!faceapi.nets.tinyFaceDetector || !faceapi.nets.tinyFaceDetector
                                                    .isLoaded) {
                                                    console.warn(
                                                        'TinyFaceDetector not loaded yet, skipping this image');
                                                    return null;
                                                }
                                                // Gunakan parameter yang sama dengan saat presensi (inputSize 224)
                                                detections = await faceapi.detectSingleFace(
                                                    img, new faceapi.TinyFaceDetectorOptions({
                                                        inputSize: 160, // Optimized dari 224 untuk kecepatan loading
                                                        scoreThreshold: 0.3 // Sama dengan saat presensi
                                                    })
                                                ).withFaceLandmarks().withFaceDescriptor();
                                            } else {
                                                // Pastikan SsdMobilenetv1 sudah dimuat
                                                if (!faceapi.nets.ssdMobilenetv1 || !faceapi.nets.ssdMobilenetv1
                                                    .isLoaded) {
                                                    console.warn(
                                                        'SsdMobilenetv1 not loaded yet, skipping this image');
                                                    return null;
                                                }
                                                // Gunakan parameter yang sama dengan saat presensi (minConfidence 0.4)
                                                detections = await faceapi.detectSingleFace(
                                                    img, new faceapi.SsdMobilenetv1Options({
                                                        minConfidence: 0.4 // Sama dengan saat presensi
                                                    })
                                                ).withFaceLandmarks().withFaceDescriptor();
                                            }
                                        } catch (modelError) {
                                            console.error(`Model error while processing ${faceData.wajah}:`,
                                                modelError);
                                            // Jika error karena model belum dimuat, tunggu sebentar dan retry
                                            if (modelError.message && modelError.message.includes(
                                                    'load model before inference')) {
                                                console.log('Model not ready, waiting 200ms before retry...');
                                                await new Promise(resolve => setTimeout(resolve, 200));
                                                // Retry sekali
                                                try {
                                                    if (isMobile) {
                                                        detections = await faceapi.detectSingleFace(
                                                            img, new faceapi.TinyFaceDetectorOptions({
                                                                inputSize: 160,
                                                                scoreThreshold: 0.5
                                                            })
                                                        ).withFaceLandmarks().withFaceDescriptor();
                                                    } else {
                                                        detections = await faceapi.detectSingleFace(
                                                            img, new faceapi.SsdMobilenetv1Options({
                                                                minConfidence: 0.5
                                                            })
                                                        ).withFaceLandmarks().withFaceDescriptor();
                                                    }
                                                } catch (retryError) {
                                                    console.error(`Retry failed for ${faceData.wajah}:`, retryError);
                                                    return null;
                                                }
                                            } else {
                                                return null;
                                            }
                                        }

                                        if (detections) {
                                            return {
                                                descriptor: detections.descriptor,
                                                wajahFile: faceData.wajah
                                            };
                                        }
                                    } catch (err) {
                                        console.error(`Error processing ${faceData.wajah}:`, err);
                                    }
                                    return null;
                                });

                                // Wait semua proses selesai secara parallel
                                const results = await Promise.all(processPromises);
                                const validResults = results.filter(r => r !== null);

                                validResults.forEach(result => {
                                    descriptions.push(result.descriptor);
                                    wajahFiles.push(result.wajahFile);
                                    validFaceFound = true;
                                });

                                // Simpan ke cache untuk next time
                                if (validFaceFound && window.FaceModelCache && typeof window.FaceModelCache.saveDescriptors ===
                                    'function') {
                                    await window.FaceModelCache.saveDescriptors(nik, descriptions, wajahFiles);
                                    console.log(`[Presensi] Descriptors cached for ${nik}`);
                                }

                                if (!validFaceFound) {
                                    console.warn(`Tidak ditemukan wajah valid untuk ${label}`);
                                    namakaryawan = "unknown";
                                } else {
                                    namakaryawan = label;
                                }

                                return new faceapi.LabeledFaceDescriptors(namakaryawan, descriptions);
                            })
                        );

                        // Hapus indikator loading setelah data wajah dimuat
                        const faceDataLoadingEl = document.getElementById('face-data-loading');
                        if (faceDataLoadingEl) faceDataLoadingEl.remove();
                        return result;
                    } catch (error) {
                        console.error('Error dalam getLabeledFaceDescriptions:', error);
                        const faceDataLoadingEl = document.getElementById('face-data-loading');
                        if (faceDataLoadingEl) faceDataLoadingEl.remove();
                        throw error;
                    }
                }

                // Fungsi untuk memastikan model benar-benar siap
                async function ensureModelsLoaded() {
                    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                    // Cek apakah model sudah dimuat dengan mencoba mengakses properti isLoaded
                    // Jika tidak ada properti isLoaded, kita coba langsung load model
                    let maxRetries = 50; // Max 5 detik (50 x 100ms)
                    let retries = 0;

                    while (retries < maxRetries) {
                        try {
                            const detectorLoaded = isMobile ?
                                (faceapi.nets.tinyFaceDetector && faceapi.nets.tinyFaceDetector.isLoaded) :
                                (faceapi.nets.ssdMobilenetv1 && faceapi.nets.ssdMobilenetv1.isLoaded);

                            const recognitionLoaded = faceapi.nets.faceRecognitionNet && faceapi.nets.faceRecognitionNet.isLoaded;
                            const landmarkLoaded = faceapi.nets.faceLandmark68Net && faceapi.nets.faceLandmark68Net.isLoaded;

                            if (detectorLoaded && recognitionLoaded && landmarkLoaded) {
                                console.log('All models confirmed loaded');
                                return true;
                            }

                            retries++;
                            await new Promise(resolve => setTimeout(resolve, 100));
                        } catch (error) {
                            console.warn('Error checking model status:', error);
                            retries++;
                            await new Promise(resolve => setTimeout(resolve, 100));
                        }
                    }

                    // Jika masih belum loaded setelah max retries, log warning
                    console.warn('Models may not be fully loaded, proceeding anyway...');
                    return true; // Proceed anyway, let face-api handle errors
                }

                // Variable untuk tracking retry
                let faceRecognitionRetries = 0;
                const maxFaceRecognitionRetries = 10; // Stop after 10 attempts (approx 10-20 seconds)

                async function startFaceRecognition() {
                    try {
                        // Stop if permission denied or retries exceeded
                        if (cameraPermissionDenied) {
                            console.warn('Camera permission denied, stopping face recognition');
                            return;
                        }

                        if (faceRecognitionRetries >= maxFaceRecognitionRetries) {
                            console.error('Max face recognition retries reached. Stopping.');
                            // Show user friendly error
                            const faceDetectionEl = document.getElementById('facedetection');
                            if (faceDetectionEl) {
                                const errorMsg = document.createElement('div');
                                errorMsg.innerHTML = '<div class="alert alert-danger">Gagal memulai kamera. Silakan refresh halaman.</div>';
                                errorMsg.style.position = 'absolute';
                                errorMsg.style.bottom = '10px';
                                errorMsg.style.width = '100%';
                                errorMsg.style.zIndex = '2000';
                                errorMsg.style.textAlign = 'center';
                                faceDetectionEl.appendChild(errorMsg);
                            }
                            return;
                        }

                        // Pastikan model benar-benar sudah dimuat sebelum digunakan
                        await ensureModelsLoaded();

                        const labeledFaceDescriptors = await getLabeledFaceDescriptions();
                        // Threshold 0.5 untuk keseimbangan (tengah-tengah)
                        // Distance < threshold = dikenali, > threshold = unknown
                        const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.5);

                        const video = document.querySelector('.webcam-capture video');

                        if (!video) {
                            console.error('Video element tidak ditemukan');
                            faceRecognitionRetries++;
                            // Exponential backoff for retry
                            const delay = Math.min(1000 * Math.pow(1.5, faceRecognitionRetries), 5000);
                            console.log(`Retrying in ${delay}ms (Attempt ${faceRecognitionRetries}/${maxFaceRecognitionRetries})`);
                            setTimeout(startFaceRecognition, delay);
                            return;
                        }

                        // Reset retries on success
                        faceRecognitionRetries = 0;

                        // Tunggu video benar-benar ready dengan lebih patient
                        if (!video.videoWidth || !video.videoHeight || video.readyState < 2) {
                            console.log('Video belum ready, waiting... readyState:', video.readyState);
                            setTimeout(startFaceRecognition, 500);
                            return;
                        }

                        console.log('Video ready:', video.videoWidth, 'x', video.videoHeight);

                        // Dapatkan parent element terlebih dahulu
                        const parent = video.parentElement;
                        if (!parent) {
                            console.error('Parent video tidak ditemukan');
                            return;
                        }

                        // Periksa apakah canvas sudah ada untuk menghindari duplikasi
                        const existingCanvas = parent.querySelector('canvas');
                        if (existingCanvas) {
                            console.log('Canvas sudah ada, menghapus yang lama');
                            existingCanvas.remove();
                        }

                        const canvas = faceapi.createCanvasFromMedia(video);

                        // Tunggu sebentar untuk memastikan video dimensions sudah stabil
                        await new Promise(resolve => setTimeout(resolve, 100));

                        // Set dimensi canvas sesuai dengan video
                        const videoWidth = video.videoWidth || video.clientWidth;
                        const videoHeight = video.videoHeight || video.clientHeight;

                        console.log('Setting canvas dimensions:', videoWidth, 'x', videoHeight);

                        canvas.width = videoWidth;
                        canvas.height = videoHeight;
                        canvas.style.position = 'absolute';
                        canvas.style.top = '0';
                        canvas.style.left = '0';
                        canvas.style.width = '100%';
                        canvas.style.height = '100%';
                        canvas.style.pointerEvents = 'none';
                        canvas.style.zIndex = '10'; // Pastikan canvas di atas video

                        // Mirror canvas jika video di-mirror
                        const videoStyle = window.getComputedStyle(video);
                        if (videoStyle.transform.includes('matrix(-1')) {
                            canvas.style.transform = 'scaleX(-1)';
                        }

                        // Append canvas ke parent yang sama dengan video
                        parent.appendChild(canvas);
                        console.log('Canvas berhasil ditambahkan ke parent');

                        // --- ABSEN BUTTONS ---
                        let absenButtons = [document.getElementById('absenmasuk'), document.getElementById('absenpulang')];
                        absenButtons.forEach(btn => btn.disabled = true);

                        const ctx = canvas.getContext("2d");
                        if (!ctx) {
                            console.error('Tidak bisa mendapatkan canvas context');
                            return;
                        }

                        const displaySize = {
                            width: videoWidth,
                            height: videoHeight
                        };
                        faceapi.matchDimensions(canvas, displaySize);

                        console.log('Face recognition setup completed, starting detection...');

                        // OPTIMASI: Variable untuk deteksi yang lebih cepat dan responsif
                        let lastDetectionTime = 0;
                        let detectionInterval = isMobile ? 150 : 80; // Interval lebih cepat untuk responsivitas
                        let isProcessing = false;
                        let consecutiveMatches = 0;
                        let requiredConsecutiveMatches = isMobile ? 1 : 1; // Langsung terdeteksi

                        // OPTIMASI: Sistem anti-flicker yang lebih ringan
                        let stableDetectionCount = 0;
                        let noFaceCount = 0;
                        const minStableFrames = isMobile ? 1 : 2; // Lebih cepat untuk stabilitas
                        const maxNoFaceFrames = isMobile ? 3 : 4; // Lebih cepat reset

                        // State tracking untuk smoothing (dikurangi untuk performa)
                        let lastValidDetection = null;
                        let detectionHistory = [];
                        const historySize = isMobile ? 2 : 3; // Dikurangi untuk performa

                        // Smoothing untuk match distance (dikurangi untuk performa)
                        let matchDistanceHistory = [];
                        const matchDistanceHistorySize = isMobile ? 2 : 3; // Dikurangi untuk performa
                        let lastMatchResult = null; // true = dikenali, false = tidak dikenali

                        async function detectFaces() {
                            try {
                                // Pastikan video masih aktif
                                if (video.paused || video.ended) {
                                    console.log('Video tidak aktif, menghentikan deteksi');
                                    return [];
                                }

                                if (isMobile) {
                                    // OPTIMASI: Gunakan inputSize 160 untuk processing lebih cepat
                                    const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                                            inputSize: 160, // Lebih kecil = lebih cepat
                                            scoreThreshold: 0.4 // Threshold optimal untuk akurasi vs kecepatan
                                        }))
                                        .withFaceLandmarks()
                                        .withFaceDescriptor();
                                    return detection ? [detection] : [];
                                } else {
                                    const detection = await faceapi.detectSingleFace(video, new faceapi.SsdMobilenetv1Options({
                                            minConfidence: 0.5 // Threshold optimal
                                        }))
                                        .withFaceLandmarks()
                                        .withFaceDescriptor();
                                    return detection ? [detection] : [];
                                }
                            } catch (error) {
                                console.error("Error dalam deteksi wajah:", error);
                                return [];
                            }
                        }

                        function updateCanvas() {
                            // Periksa apakah video dan canvas masih valid
                            if (!video || !canvas || !ctx) {
                                console.error('Video, canvas atau context tidak valid');
                                return;
                            }

                            // Periksa apakah video masih memiliki dimensi valid
                            if (!video.videoWidth || !video.videoHeight) {
                                console.log('Video dimensions tidak valid, menunggu...');
                                setTimeout(updateCanvas, 500);
                                return;
                            }

                            if (!isProcessing) {
                                const now = Date.now();
                                if (now - lastDetectionTime > detectionInterval) {
                                    isProcessing = true;
                                    lastDetectionTime = now;

                                    detectFaces()
                                        .then(detections => {
                                            const resizedDetections = faceapi.resizeResults(detections, displaySize);

                                            // PERBAIKAN: Update detection history untuk smoothing
                                            const hasFace = resizedDetections && resizedDetections.length > 0;
                                            detectionHistory.push(hasFace);
                                            if (detectionHistory.length > historySize) {
                                                detectionHistory.shift();
                                            }

                                            // Hitung persentase deteksi positif dalam history
                                            const positiveDetections = detectionHistory.filter(d => d).length;
                                            const detectionRatio = positiveDetections / detectionHistory.length;

                                            // OPTIMASI: Stabilitas lebih cepat dengan threshold lebih rendah
                                            if (hasFace && detectionRatio >= 0.5) { // 50% dari history (lebih cepat)
                                                stableDetectionCount++;
                                                noFaceCount = 0;
                                                lastValidDetection = resizedDetections[0];
                                            } else if (!hasFace) {
                                                noFaceCount++;
                                                if (noFaceCount >= maxNoFaceFrames) {
                                                    stableDetectionCount = 0;
                                                    lastValidDetection = null;
                                                }
                                            }

                                            ctx.clearRect(0, 0, canvas.width, canvas.height);

                                            // Reset status deteksi
                                            faceRecognitionDetected = 0;

                                            // PERBAIKAN: Tampilkan deteksi hanya jika sudah stabil
                                            const shouldShowDetection = stableDetectionCount >= minStableFrames && lastValidDetection;

                                            if (shouldShowDetection) {
                                                const detection = lastValidDetection;

                                                if (detection && detection.descriptor) {
                                                    const match = faceMatcher.findBestMatch(detection.descriptor);

                                                    const box = detection.detection.box;
                                                    const isUnknown = match.toString().includes("unknown");

                                                    // OPTIMASI: Smoothing lebih sederhana untuk performa
                                                    matchDistanceHistory.push(match.distance);
                                                    if (matchDistanceHistory.length > matchDistanceHistorySize) {
                                                        matchDistanceHistory.shift();
                                                    }

                                                    // Gunakan distance terbaru jika history masih sedikit, rata-rata jika sudah cukup
                                                    const avgDistance = matchDistanceHistory.length >= matchDistanceHistorySize ?
                                                        matchDistanceHistory.reduce((a, b) => a + b, 0) / matchDistanceHistory.length :
                                                        match.distance; // Langsung gunakan distance terbaru

                                                    // Threshold 0.5 untuk keseimbangan
                                                    const isNotRecognized = avgDistance > 0.5;

                                                    // Menentukan warna berdasarkan kondisi
                                                    let boxColor, labelColor, labelText;

                                                    // OPTIMASI: Langsung gunakan hasil terbaru tanpa smoothing berlebihan
                                                    let currentMatchResult = !isUnknown && !isNotRecognized;

                                                    if (isUnknown || !currentMatchResult) {
                                                        // Wajah tidak dikenali - warna kuning
                                                        boxColor = '#FFC107';
                                                        labelColor = 'rgba(255, 193, 7, 0.8)';
                                                        labelText = 'Wajah Tidak Dikenali';
                                                        consecutiveMatches = 0;
                                                        lastMatchResult = false;
                                                    } else {
                                                        // Wajah dikenali - warna hijau
                                                        boxColor = '#4CAF50';
                                                        labelColor = 'rgba(76, 175, 80, 0.8)';
                                                        labelText = "{{ $karyawan->nama_karyawan }}";
                                                        consecutiveMatches++;
                                                        if (consecutiveMatches >= requiredConsecutiveMatches) {
                                                            faceRecognitionDetected = 1;
                                                        }
                                                        lastMatchResult = true;
                                                    }

                                                    // Menggunakan style modern untuk box deteksi wajah
                                                    ctx.strokeStyle = boxColor;
                                                    ctx.lineWidth = 3;
                                                    ctx.lineJoin = 'round';
                                                    ctx.lineCap = 'round';

                                                    // Fungsi menggambar kotak dengan sudut membulat
                                                    function drawRoundedRect(ctx, x, y, width, height, radius) {
                                                        ctx.beginPath();
                                                        ctx.moveTo(x + radius, y);
                                                        ctx.lineTo(x + width - radius, y);
                                                        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
                                                        ctx.lineTo(x + width, y + height - radius);
                                                        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
                                                        ctx.lineTo(x + radius, y + height);
                                                        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
                                                        ctx.lineTo(x, y + radius);
                                                        ctx.quadraticCurveTo(x, y, x + radius, y);
                                                        ctx.closePath();
                                                        ctx.stroke();
                                                    }

                                                    // Gambar kotak deteksi wajah selalu persegi (square) dan terpusat
                                                    const squareSize = Math.min(box.width, box.height);
                                                    const squareX = box.x + (box.width - squareSize) / 2;
                                                    const squareY = box.y + (box.height - squareSize) / 2;

                                                    // Kotak modern dengan efek glow
                                                    ctx.save();
                                                    ctx.shadowColor = boxColor.includes('#4CAF50') ? 'rgba(76, 175, 80, 0.6)' :
                                                        'rgba(255, 193, 7, 0.6)';
                                                    ctx.shadowBlur = 18;
                                                    ctx.strokeStyle = boxColor;
                                                    ctx.lineWidth = 3;
                                                    drawRoundedRect(ctx, squareX, squareY, squareSize, squareSize, 16);
                                                    ctx.restore();

                                                    // Garis pandu horizontal
                                                    ctx.strokeStyle = 'rgba(255, 255, 255, 0.3)';
                                                    ctx.lineWidth = 1;
                                                    ctx.setLineDash([5, 5]);

                                                    ctx.beginPath();
                                                    ctx.moveTo(box.x, box.y + box.height / 3);
                                                    ctx.lineTo(box.x + box.width, box.y + box.height / 3);
                                                    ctx.stroke();

                                                    ctx.beginPath();
                                                    ctx.moveTo(box.x, box.y + (box.height * 2) / 3);
                                                    ctx.lineTo(box.x + box.width, box.y + (box.height * 2) / 3);
                                                    ctx.stroke();

                                                    // Garis pandu vertikal
                                                    ctx.beginPath();
                                                    ctx.moveTo(box.x + box.width / 3, box.y);
                                                    ctx.lineTo(box.x + box.width / 3, box.y + box.height);
                                                    ctx.stroke();

                                                    ctx.beginPath();
                                                    ctx.moveTo(box.x + (box.width * 2) / 3, box.y);
                                                    ctx.lineTo(box.x + (box.width * 2) / 3, box.y + box.height);
                                                    ctx.stroke();

                                                    // Reset line style
                                                    ctx.setLineDash([]);

                                                    // Label dengan style modern
                                                    const fontSize = 13;
                                                    ctx.font = `${fontSize}px 'Arial', sans-serif`;
                                                    const textWidth = ctx.measureText(labelText).width;

                                                    // Background label lebih rapat dan proporsional
                                                    const labelPadding = 3;
                                                    const labelHeight = fontSize + labelPadding * 2;
                                                    const labelWidth = Math.max(textWidth + labelPadding * 2, squareSize * 0.6);
                                                    const labelX = squareX + (squareSize - labelWidth) / 2;
                                                    const labelY = squareY + squareSize + 4;

                                                    // Gambar background label dengan sudut membulat
                                                    ctx.fillStyle = labelColor;
                                                    ctx.beginPath();
                                                    ctx.moveTo(labelX + 8, labelY);
                                                    ctx.lineTo(labelX + labelWidth - 8, labelY);
                                                    ctx.quadraticCurveTo(labelX + labelWidth, labelY, labelX + labelWidth, labelY + 8);
                                                    ctx.lineTo(labelX + labelWidth, labelY + labelHeight - 8);
                                                    ctx.quadraticCurveTo(labelX + labelWidth, labelY + labelHeight, labelX + labelWidth -
                                                        8, labelY + labelHeight);
                                                    ctx.lineTo(labelX + 8, labelY + labelHeight);
                                                    ctx.quadraticCurveTo(labelX, labelY + labelHeight, labelX, labelY + labelHeight - 8);
                                                    ctx.lineTo(labelX, labelY + 8);
                                                    ctx.quadraticCurveTo(labelX, labelY, labelX + 8, labelY);
                                                    ctx.closePath();
                                                    ctx.fill();

                                                    // Teks label
                                                    ctx.fillStyle = 'white';
                                                    ctx.textAlign = 'center';
                                                    ctx.textBaseline = 'middle';
                                                    ctx.fillText(labelText, squareX + squareSize / 2, labelY + labelHeight / 2);

                                                    // Update status tombol absen
                                                    absenButtons.forEach(btn => btn.disabled = false);
                                                }
                                            } else if (noFaceCount >= maxNoFaceFrames) {
                                                // Tampilkan label di tengah canvas dengan tampilan menarik
                                                const label = "Wajah Tidak Terdeteksi";
                                                const fontSize = 28;
                                                ctx.font = `bold ${fontSize}px Arial`;
                                                ctx.textAlign = "center";
                                                ctx.textBaseline = "middle";
                                                const centerX = canvas.width / 2;
                                                const centerY = canvas.height / 2;

                                                // Ukuran background
                                                const paddingX = 32;
                                                const paddingY = 18;
                                                const textWidth = ctx.measureText(label).width;
                                                const boxWidth = textWidth + paddingX * 2;
                                                const boxHeight = fontSize + paddingY * 2;
                                                const boxX = centerX - boxWidth / 2;
                                                const boxY = centerY - boxHeight / 2;

                                                // Background semi transparan & rounded
                                                ctx.save();
                                                ctx.globalAlpha = 0.85;
                                                ctx.fillStyle = "#F44336";
                                                ctx.beginPath();
                                                ctx.moveTo(boxX + 16, boxY);
                                                ctx.lineTo(boxX + boxWidth - 16, boxY);
                                                ctx.quadraticCurveTo(boxX + boxWidth, boxY, boxX + boxWidth, boxY + 16);
                                                ctx.lineTo(boxX + boxWidth, boxY + boxHeight - 16);
                                                ctx.quadraticCurveTo(boxX + boxWidth, boxY + boxHeight, boxX + boxWidth - 16, boxY +
                                                    boxHeight);
                                                ctx.lineTo(boxX + 16, boxY + boxHeight);
                                                ctx.quadraticCurveTo(boxX, boxY + boxHeight, boxX, boxY + boxHeight - 16);
                                                ctx.lineTo(boxX, boxY + 16);
                                                ctx.quadraticCurveTo(boxX, boxY, boxX + 16, boxY);
                                                ctx.closePath();
                                                ctx.fill();
                                                ctx.restore();

                                                // Efek shadow/glow pada teks
                                                ctx.save();
                                                ctx.shadowColor = "#fff";
                                                ctx.shadowBlur = 8;
                                                ctx.fillStyle = "#fff";
                                                ctx.fillText(label, centerX, centerY);
                                                ctx.restore();

                                                // Disable tombol absen
                                                absenButtons.forEach(btn => btn.disabled = true);
                                            }

                                            isProcessing = false;
                                        })
                                        .catch(err => {
                                            console.error("Error dalam deteksi wajah:", err);
                                            isProcessing = false;
                                        });
                                }
                            }

                            // OPTIMASI: Gunakan requestAnimationFrame untuk semua device (lebih smooth)
                            requestAnimationFrame(updateCanvas);
                        }

                        // Mulai loop animasi
                        updateCanvas();

                    } catch (error) {
                        console.error("Error starting face recognition:", error);
                        
                        faceRecognitionRetries++;
                        if (faceRecognitionRetries < maxFaceRecognitionRetries) {
                            // Coba inisialisasi ulang face recognition jika terjadi error
                            setTimeout(() => {
                                console.log('Retrying face recognition initialization after error');
                                startFaceRecognition();
                            }, 2000);
                        } else {
                            console.error('Max retries reached after error. Stopping.');
                        }
                    }
                }
            }

            function showPermissionWarning(type) {
                const messages = {
                    camera: 'Akses kamera diperlukan untuk proses presensi. Mohon izinkan kamera terlebih dahulu.',
                    location: 'Akses lokasi diperlukan untuk proses presensi. Mohon aktifkan dan izinkan lokasi.'
                };
                Swal.fire({
                    icon: 'warning',
                    title: 'Izin Diperlukan',
                    text: messages[type] || 'Mohon lengkapi perizinan yang dibutuhkan.'
                });
            }


            // Helper function to convert dataURI to Blob
            function dataURItoBlob(dataURI) {
                var byteString = atob(dataURI.split(',')[1]);
                var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
                var ab = new ArrayBuffer(byteString.length);
                var ia = new Uint8Array(ab);
                for (var i = 0; i < byteString.length; i++) {
                    ia[i] = byteString.charCodeAt(i);
                }
                return new Blob([ab], {
                    type: mimeString
                });
            }

            $("#absenmasuk").click(function() {
                if (cameraPermissionDenied) {
                    showPermissionWarning('camera');
                    return;
                }
                if (locationPermissionDenied) {
                    showPermissionWarning('location');
                    return;
                }



                // alert(lokasi);
                $("#absenmasuk").prop('disabled', true);
                $("#absenpulang").prop('disabled', true);
                $("#absenmasuk").html(
                    '<div class="spinner-border text-light mr-2" role="status"><span class="sr-only">Loading...</span></div> <span style="font-size:16px">Loading...</span>'

                );
                let status = '1';
                Webcam.snap(function(uri) {
                    image = uri;
                });

                // alert(faceRecognitionDetected);
                // return false;
                if (faceRecognitionDetected == 0 && faceRecognition == 1) {
                    swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Wajah tidak terdeteksi',
                        didClose: function() {
                            $("#absenmasuk").prop('disabled', false);
                            $("#absenpulang").prop('disabled', false);
                            $("#absenmasuk").html(
                                '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Masuk</span>'
                            );
                            $("#absenpulang").html(
                                '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Pulang</span>'
                            )
                        }
                    })
                    return false;
                } else {
                    var blob = dataURItoBlob(image);
                    var formData = new FormData();
                    formData.append('image', blob, 'image.png'); // Send as file
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('status', status);
                    formData.append('lokasi', lokasi);
                    formData.append('lokasi_cabang', lokasi_cabang);
                    formData.append('kode_jam_kerja', "{{ $jam_kerja->kode_jam_kerja }}");

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('presensi.store') }}",
                        data: formData, // Use FormData
                        processData: false, // Prevent jQuery from processing the data
                        contentType: false, // Prevent jQuery from setting contentType
                        cache: false,
                        success: function(data) {
                            if (data.status == true) {
                                notifikasi_absenmasuk.play();
                                swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 4000
                                }).then(function() {
                                    window.location.href = '/dashboard';
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON.notifikasi == "notifikasi_radius") {
                                notifikasi_radius.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_mulaiabsen") {
                                notifikasi_mulaiabsen.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_akhirabsen") {
                                notifikasi_akhirabsen.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_sudahabsen") {
                                notifikasi_sudahabsen.play();
                            }
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON.message,
                                didClose: function() {
                                    $("#absenmasuk").prop('disabled', false);
                                    $("#absenpulang").prop('disabled', false);
                                    $("#absenmasuk").html(
                                        '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Masuk</span>'
                                    );
                                    $("#absenpulang").html(
                                        '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Pulang</span>'
                                    )
                                }

                            });
                        }
                    });
                }

            });

            $("#absenpulang").click(function() {
                if (cameraPermissionDenied) {
                    showPermissionWarning('camera');
                    return;
                }
                if (locationPermissionDenied) {
                    showPermissionWarning('location');
                    return;
                }
                // alert(lokasi);
                $("#absenmasuk").prop('disabled', true);
                $("#absenpulang").prop('disabled', true);
                $("#absenpulang").html(
                    '<div class="spinner-border text-light mr-2" role="status"><span class="sr-only">Loading...</span></div> <span style="font-size:16px">Loading...</span>'

                );
                let status = '2';
                Webcam.snap(function(uri) {
                    image = uri;
                });
                if (faceRecognitionDetected == 0 && faceRecognition == 1) {
                    swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Wajah tidak terdeteksi',
                        didClose: function() {
                            $("#absenmasuk").prop('disabled', false);
                            $("#absenpulang").prop('disabled', false);
                            $("#absenpulang").html(
                                '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Pulang</span>'
                            );
                        }
                    })
                    return false;
                } else {
                    var blob = dataURItoBlob(image);
                    var formData = new FormData();
                    formData.append('image', blob, 'image.png');
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('status', status);
                    formData.append('lokasi', lokasi);
                    formData.append('lokasi_cabang', lokasi_cabang);
                    formData.append('kode_jam_kerja', "{{ $jam_kerja->kode_jam_kerja }}");

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('presensi.store') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function(data) {
                            if (data.status == true) {
                                notifikasi_absenpulang.play();
                                swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 4000
                                }).then(function() {
                                    window.location.href = '/dashboard';
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON.notifikasi == "notifikasi_radius") {
                                notifikasi_radius.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_mulaiabsen") {
                                notifikasi_mulaiabsen.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_akhirabsen") {
                                notifikasi_akhirabsen.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_sudahabsen") {
                                notifikasi_sudahabsenpulang.play();
                            }
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON.message,
                                didClose: function() {
                                    $("#absenmasuk").prop('disabled', false);
                                    $("#absenpulang").prop('disabled', false);
                                    $("#absenpulang").html(
                                        '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Pulang</span>'
                                    );
                                }

                            });
                        }
                    });
                }
            });

            $("#cabang").change(function() {
                // Ambil nilai lokasi cabang yang dipilih
                lokasi_cabang = $(this).val();
                console.log("Lokasi cabang berubah: " + lokasi_cabang);

                // Ambil teks cabang yang dipilih
                let cabangText = $("#cabang option:selected").text();

                // Tampilkan notifikasi cabang berubah
                swal.fire({
                    icon: 'info',
                    title: 'Lokasi Berubah',
                    text: 'Lokasi cabang berubah menjadi: ' + cabangText,
                    showConfirmButton: false,
                    timer: 2000
                });

                // Jika lokasi cabang berubah, reload peta
                if (typeof map !== 'undefined' && map !== null) {
                    map.remove(); // Hapus peta sebelumnya
                }

                // Tampilkan indikator loading
                document.getElementById('map-loading').style.display = 'block';

                try {
                    // Buat array dari string lokasi
                    var lok = lokasi_cabang.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];

                    // Inisialisasi peta baru dengan lokasi cabang yang dipilih


                    // Jika geolocation tersedia, tambahkan marker lokasi user
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                                // Update lokasi user
                                lokasi = position.coords.latitude + "," + position.coords.longitude;
                                map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);

                                // Tambahkan tile layer
                                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    maxZoom: 19,
                                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                                }).addTo(map);
                                // Tambahkan marker untuk lokasi user
                                var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);

                                // Tambahkan lingkaran radius
                                var radius = "{{ $lokasi_kantor->radius_cabang }}";
                                var circle = L.circle([lat_kantor, long_kantor], {
                                    color: 'red',
                                    fillColor: '#f03',
                                    fillOpacity: 0.5,
                                    radius: radius
                                }).addTo(map);

                                // Sembunyikan indikator loading
                                document.getElementById('map-loading').style.display = 'none';
                            },
                            function(error) {
                                // Tangani error geolocation
                                console.error("Error getting geolocation:", error);

                                // Tambahkan lingkaran radius tanpa marker user
                                var radius = "{{ $lokasi_kantor->radius_cabang }}";
                                var circle = L.circle([lat_kantor, long_kantor], {
                                    color: 'red',
                                    fillColor: '#f03',
                                    fillOpacity: 0.5,
                                    radius: radius
                                }).addTo(map);

                                // Sembunyikan indikator loading
                                document.getElementById('map-loading').style.display = 'none';

                                // Tampilkan pesan error
                                document.getElementById('map-loading').innerHTML =
                                    'Gagal mendapatkan lokasi. Silakan cek izin lokasi.';
                                document.getElementById('map-loading').style.display = 'block';
                                setTimeout(function() {
                                    document.getElementById('map-loading').style.display =
                                        'none';
                                }, 3000);
                            });
                    } else {
                        // Jika geolocation tidak didukung
                        // Tambahkan lingkaran radius tanpa marker user
                        var radius = "{{ $lokasi_kantor->radius_cabang }}";
                        var circle = L.circle([lat_kantor, long_kantor], {
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.5,
                            radius: radius
                        }).addTo(map);

                        // Sembunyikan indikator loading
                        document.getElementById('map-loading').style.display = 'none';

                        // Tampilkan pesan error
                        document.getElementById('map-loading').innerHTML =
                            'Geolokasi tidak didukung oleh perangkat ini.';
                        document.getElementById('map-loading').style.display = 'block';
                        setTimeout(function() {
                            document.getElementById('map-loading').style.display = 'none';
                        }, 3000);
                    }
                } catch (error) {
                    console.error("Error initializing map:", error);
                    document.getElementById('map-loading').innerHTML =
                        'Gagal memuat peta. Silakan coba lagi.';
                    document.getElementById('map-loading').style.display = 'block';
                    setTimeout(function() {
                        document.getElementById('map-loading').style.display = 'none';
                    }, 3000);
                }
            });
        });
    </script>
@endpush
