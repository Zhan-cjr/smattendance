<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="{{ $t['primary'] ?? '#0f766e' }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Karyawan - SM-Attendance</title>

    <!-- Font Awesome 6 & Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Inter"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '{{ $t["primary"] ?? "#0f766e" }}',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    },
                    boxShadow: {
                        'glass': '0 8px 30px rgba(0, 0, 0, 0.08)',
                        'bento': '0 4px 20px -2px rgba(15, 23, 42, 0.06)',
                    }
                }
            }
        }
    </script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: {{ $t['bg_body'] ?? '#f8fafc' }};
            color: #0f172a;
            -webkit-tap-highlight-color: transparent;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Dark Mode Global Styles */
        body.dark, html.dark body {
            background-color: #070b14 !important;
            color: #f8fafc !important;
        }

        .hero-gradient {
            background: linear-gradient(145deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #115e59 50%, #042f2e 100%);
            border-bottom-left-radius: 36px;
            border-bottom-right-radius: 36px;
            position: relative;
            box-shadow: 0 14px 35px -8px rgba(15, 118, 110, 0.35);
        }
        .dark .hero-gradient {
            background: linear-gradient(145deg, #022c22 0%, #064e3b 50%, #031a15 100%) !important;
            box-shadow: 0 14px 35px -8px rgba(0, 0, 0, 0.8), 0 0 25px rgba(16, 185, 129, 0.15) !important;
            border-bottom: 1px solid rgba(16, 185, 129, 0.25) !important;
        }

        .glass-btn {
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 14px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-btn:active {
            transform: scale(0.92);
            background: rgba(255, 255, 255, 0.25);
        }
        .dark .glass-btn {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
        }

        /* Avatar glow */
        .avatar-ring {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.2));
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            position: relative;
        }
        .dark .avatar-ring {
            background: linear-gradient(135deg, #10b981, rgba(255,255,255,0.3)) !important;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35) !important;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: #ffffff;
        }

        /* Carousel dots */
        .dot {
            height: 6px;
            width: 6px;
            background: rgba(15, 118, 110, 0.25);
            border-radius: 50%;
            display: inline-block;
            margin: 0 3px;
            transition: all 0.3s ease;
        }
        .dot.active {
            width: 18px;
            border-radius: 10px;
            background: {{ $t['primary'] ?? '#0f766e' }};
        }
        .dark .dot {
            background: rgba(255, 255, 255, 0.2);
        }
        .dark .dot.active {
            background: #10b981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
        }

        .carousel-wrapper { width: 100%; overflow: hidden; position: relative; border-radius: 20px; }
        .carousel-track { display: flex; transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1); width: 100%; }
        .carousel-track .alert-slide { width: 100%; flex: 0 0 100%; flex-shrink: 0; box-sizing: border-box; }

        /* Modern Action Card */
        .action-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.9);
            transition: all 0.3s ease;
        }
        .dark .action-card {
            background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.7), 0 0 20px rgba(16, 185, 129, 0.08) !important;
        }

        /* App icon tiles */
        .app-tile {
            background: #ffffff;
            border-radius: 18px;
            padding: 12px 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(241, 245, 249, 1);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dark .app-tile {
            background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4) !important;
        }
        .app-tile:active {
            transform: scale(0.93);
            background: #f8fafc;
        }
        .dark .app-tile:active {
            background: #1e293b !important;
        }

        .app-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;
            font-size: 1.25rem;
            transition: transform 0.2s;
        }

        /* Presence list card */
        .presence-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 12px 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
            transition: all 0.15s ease;
        }
        .dark .presence-card {
            background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
        }
        .presence-card:active {
            transform: scale(0.98);
        }

        /* Dark Mode High-Contrast Overrides */
        .dark .bg-white {
            background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        .dark .bg-slate-50 {
            background-color: #182339 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .dark .bg-slate-100 {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .dark .text-slate-800, .dark .text-slate-900 {
            color: #f8fafc !important;
        }
        .dark .text-slate-700 {
            color: #e2e8f0 !important;
        }
        .dark .text-slate-600 {
            color: #cbd5e1 !important;
        }
        .dark .text-slate-500, .dark .text-slate-400 {
            color: #94a3b8 !important;
        }
        .dark .border-slate-100, .dark .border-slate-200 {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
    </style>
</head>
<body class="bg-slate-50">
    {{-- PWA Service Worker --}}
    <x-pwa-service-worker-register />
    <x-live-location-tracker />

    <div class="max-w-md mx-auto min-h-screen pb-24 relative overflow-x-hidden">

        {{-- ===== HERO SECTION ===== --}}
        <div class="hero-gradient px-5 pt-6 pb-16 text-white">
            <!-- Top Controls -->
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('karyawan-approval.index') }}" class="glass-btn relative">
                        <ion-icon name="notifications-outline" style="font-size:22px;"></ion-icon>
                        @if (isset($pendingApprovalCount) && $pendingApprovalCount > 0)
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 rounded-full border-2 border-teal-800 text-[10px] font-extrabold flex items-center justify-center px-1">
                                {{ $pendingApprovalCount }}
                            </span>
                        @endif
                    </a>

                    {{-- Theme Toggle Button (Light/Dark Mode) --}}
                    <button type="button" onclick="toggleTheme()" class="glass-btn" id="themeToggleBtn" title="Ganti Tema">
                        <ion-icon name="moon-outline" id="themeIcon" style="font-size:20px;"></ion-icon>
                    </button>
                </div>
                
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-semibold bg-white/15 px-3 py-1 rounded-full backdrop-blur-md border border-white/20 flex items-center gap-1">
                        <i class="fa-solid fa-location-dot text-[10px] text-emerald-300"></i>
                        <span>{{ $karyawan->nama_cabang ?? 'Pusat' }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="glass-btn">
                            <ion-icon name="log-out-outline" style="font-size:22px;"></ion-icon>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Profile Info & Greeting -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex-1 min-w-0 pr-3">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/15 text-white text-[11px] font-semibold mb-1.5 backdrop-blur-sm border border-white/10">
                        <span>{{ $greeting ?? 'Selamat Beraktivitas 👋' }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-white truncate leading-tight">
                        {{ $karyawan->nama_karyawan }}
                    </h3>
                    <p class="text-xs text-white/70 truncate mt-0.5">
                        {{ $karyawan->nama_jabatan }} &bull; {{ $karyawan->nama_dept }}
                    </p>
                </div>

                <a href="{{ route('profile.index') }}" class="shrink-0 active:scale-95 transition-transform">
                    <div class="avatar-ring">
                        @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                            <img src="{{ getfotoKaryawan($karyawan->foto) }}" alt="Avatar" class="avatar-img">
                        @else
                            <img src="{{ asset('assets/template/img/sample/avatar/avatar1.jpg') }}" alt="Avatar" class="avatar-img">
                        @endif
                    </div>
                </a>
            </div>

            <!-- Clock & Live Date -->
            <div class="text-center pt-1 pb-2">
                <h2 id="jam" class="text-4xl font-extrabold tracking-tight text-white drop-shadow-md">00:00:00</h2>
                <div class="text-xs text-white/80 font-medium mt-1 flex items-center justify-center gap-1.5">
                    <ion-icon name="calendar-outline"></ion-icon>
                    <span>{{ getNamaHari(date('D')) }}, {{ DateToIndo(date('Y-m-d')) }}</span>
                </div>
                
                {{-- Today Scheduled Shift Chip --}}
                <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/20 backdrop-blur-md text-[11px] font-medium text-emerald-200 border border-white/10">
                    <i class="fa-solid fa-clock text-emerald-300"></i>
                    @if ($jam_kerja_hari_ini)
                        <span>Shift Hari Ini:</span>
                        <strong class="text-white">{{ $jam_kerja_hari_ini->nama_jam_kerja }} ({{ date('H:i', strtotime($jam_kerja_hari_ini->jam_masuk)) }} - {{ date('H:i', strtotime($jam_kerja_hari_ini->jam_pulang)) }})</strong>
                    @else
                        <span class="text-white/90">Pilih Shift saat Absen Masuk</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== FLOATING ATTENDANCE ACTION CARD ===== --}}
        <div class="px-4 -mt-10 relative z-20">
            <div class="action-card p-4">
                <!-- Status Badge & Smart Action Button -->
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Status Hari Ini</span>
                        @if (!empty($presensi->jam_out))
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1.5 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Selesai Bekerja (Pulang)
                            </span>
                        @elseif (!empty($presensi->jam_in))
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Sedang Bekerja
                            </span>
                        @else
                            <span class="text-xs font-bold text-amber-500 dark:text-amber-400 flex items-center gap-1.5 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                Belum Melakukan Presensi
                            </span>
                        @endif
                    </div>

                    <!-- Dynamic Action Button -->
                    @if (empty($presensi->jam_in))
                        <a href="/presensi/create" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-white shadow-md active:scale-95 transition-all"
                           style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                            <ion-icon name="finger-print" style="font-size:16px;"></ion-icon>
                            <span>Absen Masuk</span>
                        </a>
                    @elseif (empty($presensi->jam_out))
                        <a href="/presensi/create" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-white shadow-md active:scale-95 transition-all"
                           style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
                            <ion-icon name="log-out-outline" style="font-size:16px;"></ion-icon>
                            <span>Absen Pulang</span>
                        </a>
                    @else
                        <div class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full text-xs font-bold text-emerald-700 bg-emerald-50 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                            <span>Selesai</span>
                        </div>
                    @endif
                </div>

                <!-- UNIFIED REALTIME GPS RADIUS & LIVE USER WEATHER CARD -->
                <div id="unifiedGpsWeatherContainer" class="mb-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700/60 transition-all">
                    <!-- Top Bar: GPS Status & Live Weather Pill -->
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/60 dark:border-slate-700/60">
                        <div class="flex items-center gap-2 min-w-0">
                            <div id="gpsIconBox" class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-xs shrink-0 transition-all">
                                <i class="fa-solid fa-location-crosshairs animate-spin"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span id="userGpsLocationName" class="text-xs font-extrabold text-slate-800 dark:text-slate-100 truncate">
                                        Mendeteksi Lokasi GPS...
                                    </span>
                                </div>
                                <span id="gpsStatusText" class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 truncate block">
                                    Memeriksa radius kantor...
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <!-- Weather Temp Pill (When GPS is active) -->
                            <div id="weatherPill" class="flex items-center gap-1 px-2 py-1 rounded-xl bg-white dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600 shadow-2xs">
                                <i id="weatherMainIcon" class="fa-solid fa-cloud-sun text-xs text-amber-500"></i>
                                <span id="weatherTemp" class="text-xs font-black text-slate-800 dark:text-slate-100">--°C</span>
                            </div>
                            <!-- GPS Radius Badge -->
                            <span id="gpsBadge" class="text-[10px] font-bold px-2 py-1 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 shrink-0">
                                GPS
                            </span>
                            <!-- Refresh Button -->
                            <button type="button" onclick="checkGpsRadius(true)" class="w-7 h-7 rounded-lg bg-slate-200/80 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 flex items-center justify-center text-[11px] active:scale-95 transition-all" title="Perbarui Lokasi & Cuaca">
                                <i class="fa-solid fa-arrows-rotate" id="weatherRefreshIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Mini Weather & GPS Stats -->
                    <div class="grid grid-cols-3 gap-1.5 mt-2 pt-0.5 text-center">
                        <div class="bg-white/80 dark:bg-slate-900/60 p-1.5 rounded-xl border border-slate-200/50 dark:border-slate-700/50">
                            <span class="text-[9.5px] text-slate-400 dark:text-slate-400 font-medium block">Kondisi Cuaca</span>
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate block mt-0.5" id="weatherConditionDesc">Mendeteksi...</span>
                        </div>
                        <div class="bg-white/80 dark:bg-slate-900/60 p-1.5 rounded-xl border border-slate-200/50 dark:border-slate-700/50">
                            <span class="text-[9.5px] text-slate-400 dark:text-slate-400 font-medium block">💧 Lembab & 💨 Angin</span>
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate block mt-0.5" id="weatherEnvStats">-- / --</span>
                        </div>
                        <div class="bg-white/80 dark:bg-slate-900/60 p-1.5 rounded-xl border border-slate-200/50 dark:border-slate-700/50">
                            <span class="text-[9.5px] text-slate-400 dark:text-slate-400 font-medium block">🎯 Akurasi GPS</span>
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate block mt-0.5" id="gpsAccuracy">-- m</span>
                        </div>
                    </div>

                    <!-- Unified Field & Radius Advisory -->
                    <div id="weatherAdvisoryBox" class="mt-2 p-2 rounded-xl bg-slate-200/60 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600/50 flex items-start gap-2 transition-all">
                        <div id="weatherAdvisoryIcon" class="w-5 h-5 rounded-md bg-slate-400 text-white flex items-center justify-center text-[9px] shrink-0 mt-0.5">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <p id="weatherAdvisoryText" class="text-[10.5px] text-slate-600 dark:text-slate-300 font-medium leading-snug">
                            Menghubungkan sensor GPS untuk membaca radius kantor & cuaca di titik Anda...
                        </p>
                    </div>
                </div>

                <!-- Live Work Duration Timer (Only when Clocked In & Not Clocked Out) -->
                @if (!empty($presensi->jam_in) && empty($presensi->jam_out))
                    <div class="mb-3 p-2.5 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-800/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs">
                                <i class="fa-solid fa-stopwatch animate-pulse"></i>
                            </div>
                            <div>
                                <span class="text-[10px] text-emerald-700 dark:text-emerald-300 font-medium block">Durasi Kerja Berjalan</span>
                                <span id="liveWorkDuration" class="text-xs font-extrabold text-emerald-900 dark:text-emerald-200 tracking-wider">Menghitung...</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-200/60 dark:bg-emerald-800/60 text-emerald-800 dark:text-emerald-200">Aktif</span>
                    </div>
                @endif

                <!-- Jam In & Jam Out Details -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Jam Masuk -->
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-2xl flex items-center gap-2.5 border border-slate-100 dark:border-slate-800">
                        <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center shrink-0 overflow-hidden relative group">
                            @if (!empty($presensi->foto_in) && Storage::disk('public')->exists('/uploads/absensi/' . $presensi->foto_in))
                                <img src="{{ url('/storage/uploads/absensi/' . $presensi->foto_in) }}" class="w-full h-full object-cover">
                                <a href="{{ url('/storage/uploads/absensi/' . $presensi->foto_in) }}" download class="absolute inset-0 bg-black/40 flex items-center justify-center text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            @else
                                <ion-icon name="camera" class="text-slate-400 text-lg"></ion-icon>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Jam Masuk</span>
                            <span class="text-sm font-extrabold text-slate-800 dark:text-slate-100 tracking-wide">
                                {{ !empty($presensi->jam_in) ? date('H:i', strtotime($presensi->jam_in)) : '--:--' }}
                            </span>
                        </div>
                    </div>

                    <!-- Jam Pulang -->
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-2xl flex items-center gap-2.5 border border-slate-100 dark:border-slate-800">
                        <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center shrink-0 overflow-hidden relative group">
                            @if (!empty($presensi->foto_out) && Storage::disk('public')->exists('/uploads/absensi/' . $presensi->foto_out))
                                <img src="{{ url('/storage/uploads/absensi/' . $presensi->foto_out) }}" class="w-full h-full object-cover">
                                <a href="{{ url('/storage/uploads/absensi/' . $presensi->foto_out) }}" download class="absolute inset-0 bg-black/40 flex items-center justify-center text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            @else
                                <ion-icon name="camera" class="text-slate-400 text-lg"></ion-icon>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Jam Pulang</span>
                            <span class="text-sm font-extrabold text-slate-800 dark:text-slate-100 tracking-wide">
                                {{ !empty($presensi->jam_out) ? date('H:i', strtotime($presensi->jam_out)) : '--:--' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== ALERTS CAROUSEL (KONTRAK / SP / PENGUMUMAN) ===== --}}
        @php
            $activeAlerts = [];
            if (!empty($pengumuman)) { $activeAlerts[] = 'pengumuman'; }
            if (!empty($notif_kontrak)) { $activeAlerts[] = 'kontrak'; }
            if (!empty($notif_sp)) { $activeAlerts[] = 'sp'; }
        @endphp

        @if (count($activeAlerts) > 0)
            <div class="px-4 mt-4">
                <div id="alertCarousel" class="carousel-wrapper shadow-sm">
                    <div class="carousel-track">
                        @foreach ($activeAlerts as $type)
                            @if($type == 'pengumuman')
                                <a href="{{ route('pengumuman.show', Crypt::encrypt($pengumuman->id)) }}" class="alert-slide block p-3.5 rounded-2xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 text-decoration-none hover:bg-sky-100/60 active:scale-[0.99] transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-sky-100 dark:bg-sky-900/60 text-sky-600 dark:text-sky-300">
                                            <i class="fa-solid fa-bullhorn text-base"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1">
                                                <h5 class="text-xs font-bold text-sky-900 dark:text-sky-200 truncate">{{ $pengumuman->judul }}</h5>
                                                <span class="text-[10px] text-sky-500 dark:text-sky-400 font-medium shrink-0">{{ \Carbon\Carbon::parse($pengumuman->created_at)->translatedFormat('d M Y') }}</span>
                                            </div>
                                            <div class="text-[11px] text-sky-700 dark:text-sky-300 mt-1 line-clamp-2 leading-snug">{!! strip_tags($pengumuman->isi) !!}</div>
                                            <div class="mt-2 flex items-center justify-end">
                                                <span class="text-[11px] font-bold text-sky-800 dark:text-sky-300 flex items-center gap-1 hover:underline">
                                                    <span>Baca Selengkapnya</span>
                                                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @elseif($type == 'kontrak')
                                <a href="{{ route('kontrak.index') }}" class="alert-slide block p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-decoration-none hover:bg-amber-100/60 active:scale-[0.99] transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-300">
                                            <i class="fa-solid fa-file-contract text-base"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h5 class="text-xs font-bold text-amber-900 dark:text-amber-200">Masa Kontrak Berakhir Segera</h5>
                                            <p class="text-[11px] text-amber-700 dark:text-amber-300 mt-0.5 leading-snug">
                                                Sisa masa kontrak: <strong>{{ $notif_kontrak['sisa_hari'] }} hari</strong> (Selesai: {{ $notif_kontrak['tanggal_akhir'] }}).
                                            </p>
                                            <div class="mt-2 flex items-center justify-end">
                                                <span class="text-[11px] font-bold text-amber-800 dark:text-amber-300 flex items-center gap-1 hover:underline">
                                                    <span>Lihat Detail Kontrak</span>
                                                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @elseif($type == 'sp')
                                <a href="{{ route('pelanggaran.index') }}" class="alert-slide block p-3.5 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-decoration-none hover:bg-red-100/60 active:scale-[0.99] transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-red-100 dark:bg-red-900/60 text-red-600 dark:text-red-300">
                                            <i class="fa-solid fa-triangle-exclamation text-base"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h5 class="text-xs font-bold text-red-900 dark:text-red-200">Peringatan Disiplin Aktif</h5>
                                            <p class="text-[11px] text-red-700 dark:text-red-300 mt-0.5 leading-snug">
                                                {{ $notif_sp->jenis_sp }} s/d {{ \Carbon\Carbon::parse($notif_sp->sampai)->translatedFormat('d M Y') }}.
                                            </p>
                                            <div class="mt-2 flex items-center justify-end">
                                                <span class="text-[11px] font-bold text-red-800 dark:text-red-300 flex items-center gap-1 hover:underline">
                                                    <span>Lihat Surat Peringatan</span>
                                                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                @if(count($activeAlerts) > 1)
                    <div class="flex justify-center mt-2">
                        @foreach($activeAlerts as $idx => $type)
                            <span class="dot {{ $idx == 0 ? 'active' : '' }}" data-idx="{{ $idx }}"></span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- ===== BENTO MONTHLY RECAP WITH ATTENDANCE RATE ===== --}}
        <div class="px-4 mt-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-100 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Rekap Bulan {{ $bulan_skrg }}</h4>
                        <span class="text-[10px] text-slate-400">Akumulasi performa kehadiran Anda</span>
                    </div>
                    @if (isset($attendance_rate))
                        <span class="text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 px-2.5 py-1 rounded-full flex items-center gap-1">
                            <i class="fa-solid fa-chart-line text-[9px]"></i>
                            <span>{{ $attendance_rate }}% Hadir</span>
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-5 gap-2 text-center">
                    <!-- Hadir -->
                    <div class="bg-teal-50/70 dark:bg-emerald-500/10 p-2.5 rounded-2xl border border-teal-100/50 dark:border-emerald-500/30">
                        <span class="text-lg font-black text-teal-700 dark:text-emerald-400 block leading-tight">{{ $rekappresensi->hadir ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-teal-600 dark:text-emerald-300 block mt-0.5">Hadir</span>
                    </div>

                    <!-- Lembur -->
                    <div class="bg-emerald-50/70 dark:bg-teal-500/10 p-2.5 rounded-2xl border border-emerald-100/50 dark:border-teal-500/30">
                        <span class="text-lg font-black text-emerald-700 dark:text-teal-400 block leading-tight">{{ $rekappresensi->lembur ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-emerald-600 dark:text-teal-300 block mt-0.5">Lembur</span>
                    </div>

                    <!-- Sakit -->
                    <div class="bg-amber-50/70 dark:bg-amber-500/10 p-2.5 rounded-2xl border border-amber-100/50 dark:border-amber-500/30">
                        <span class="text-lg font-black text-amber-700 dark:text-amber-400 block leading-tight">{{ $rekappresensi->sakit ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-300 block mt-0.5">Sakit</span>
                    </div>

                    <!-- Izin -->
                    <div class="bg-sky-50/70 dark:bg-sky-500/10 p-2.5 rounded-2xl border border-sky-100/50 dark:border-sky-500/30">
                        <span class="text-lg font-black text-sky-700 dark:text-sky-400 block leading-tight">{{ $rekappresensi->izin ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-sky-600 dark:text-sky-300 block mt-0.5">Izin</span>
                    </div>

                    <!-- Cuti -->
                    <div class="bg-purple-50/70 dark:bg-purple-500/10 p-2.5 rounded-2xl border border-purple-100/50 dark:border-purple-500/30">
                        <span class="text-lg font-black text-purple-700 dark:text-purple-400 block leading-tight">{{ $rekappresensi->cuti ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-purple-600 dark:text-purple-300 block mt-0.5">Cuti</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== QUICK APP GRID (8 APPS) ===== --}}
        <div class="px-4 mt-4">
            <div class="grid grid-cols-4 gap-2.5">
                <!-- 1. ID Card -->
                <a href="{{ route('karyawan.idcard', Crypt::encrypt($karyawan->nik)) }}" class="app-tile">
                    <div class="app-icon-box bg-blue-50 dark:bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-transparent dark:border-blue-500/25">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">ID Card</span>
                </a>

                <!-- 2. Istirahat / Kontrak -->
                @if ($general_setting->absen_istirahat == 1)
                    <a href="{{ route('presensiistirahat.create') }}" class="app-tile">
                        <div class="app-icon-box bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-transparent dark:border-amber-500/25">
                            <i class="fa-solid fa-mug-hot"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">Istirahat</span>
                    </a>
                @else
                    <a href="{{ route('kontrak.index') }}" class="app-tile">
                        <div class="app-icon-box bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-transparent dark:border-amber-500/25">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">Kontrak</span>
                    </a>
                @endif

                <!-- 3. Lembur -->
                <a href="{{ route('presensiistirahatlembur.create') }}" class="app-tile">
                    <div class="app-icon-box bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-transparent dark:border-emerald-500/25">
                        <i class="fa-solid fa-business-time"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">Lembur</span>
                </a>

                <!-- 4. Slip Gaji -->
                <a href="{{ route('slipgaji.index') }}" class="app-tile">
                    <div class="app-icon-box bg-teal-50 dark:bg-teal-500/15 text-teal-600 dark:text-teal-400 border border-transparent dark:border-teal-500/25">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">Slip Gaji</span>
                </a>

                <!-- 5. Aktivitas -->
                @can('aktivitaskaryawan.index')
                    <a href="{{ route('aktivitaskaryawan.index') }}" class="app-tile">
                        <div class="app-icon-box bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border border-transparent dark:border-indigo-500/25">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">Aktivitas</span>
                    </a>
                @endcan

                <!-- 6. Visit / Kunjungan -->
                @can('kunjungan.index')
                    <a href="{{ route('kunjungan.index') }}" class="app-tile">
                        <div class="app-icon-box bg-rose-50 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-transparent dark:border-rose-500/25">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">Visit</span>
                    </a>
                @endcan

                <!-- 7. Scan Wajah -->
                <a href="javascript:void(0)" id="btnDaftarkanWajah" class="app-tile">
                    <div class="app-icon-box bg-purple-50 dark:bg-purple-500/15 text-purple-600 dark:text-purple-400 border border-transparent dark:border-purple-500/25">
                        <i class="fa-solid fa-user-astronaut"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">Wajah</span>
                </a>

                <!-- 8. Lainnya -->
                <a href="{{ route('shortcut.index') }}" class="app-tile">
                    <div class="app-icon-box bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 border border-transparent dark:border-slate-600/30">
                        <i class="fa-solid fa-ellipsis"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">Lainnya</span>
                </a>
            </div>
        </div>

        {{-- ===== ATTENDANCE HISTORY TABS ===== --}}
        <div class="px-4 mt-5">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-extrabold text-slate-800 dark:text-slate-100">Riwayat Kehadiran</h4>
                <div class="flex p-1 bg-slate-200/70 dark:bg-slate-800 rounded-full text-xs font-semibold">
                    <button id="tabPresensi" onclick="switchTab('presensi')" class="px-3 py-1 rounded-full bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-sm transition-all">
                        Presensi
                    </button>
                    <button id="tabLembur" onclick="switchTab('lembur')" class="px-3 py-1 rounded-full text-slate-500 dark:text-slate-400 transition-all">
                        Lembur
                    </button>
                </div>
            </div>

            <!-- Content Tab: Presensi -->
            <div id="contentPresensi" class="space-y-2.5">
                @forelse ($datapresensi as $d)
                    @php
                        $namahari = ['Sun'=>'Minggu','Mon'=>'Senin','Tue'=>'Selasa','Wed'=>'Rabu','Thu'=>'Kamis','Fri'=>'Jumat','Sat'=>'Sabtu'];
                        $day_eng = date('D', strtotime($d->tanggal));
                        $day_indo = $namahari[$day_eng] ?? $day_eng;
                        $day_short = strtoupper(substr($day_indo, 0, 3));
                        $tgl = date('d', strtotime($d->tanggal));

                        $statusBadgeClass = 'bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-800';
                        $statusText = 'Hadir';
                        if ($d->status == 'i') { $statusBadgeClass = 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-800'; $statusText = 'Izin'; }
                        elseif ($d->status == 's') { $statusBadgeClass = 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800'; $statusText = 'Sakit'; }
                        elseif ($d->status == 'c') { $statusBadgeClass = 'bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800'; $statusText = 'Cuti'; }
                        elseif ($d->status == 'a') { $statusBadgeClass = 'bg-red-50 dark:bg-red-950/60 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800'; $statusText = 'Alpha'; }
                    @endphp

                    <div class="presence-card flex items-center gap-3 cursor-pointer"
                         onclick="showPresensiDetail('{{ $d->tanggal }}', '{{ DateToIndo($d->tanggal) }}', '{{ $d->nama_jam_kerja ?? '-' }}', '{{ $d->jam_in ? date('H:i', strtotime($d->jam_in)) : '-' }}', '{{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : '-' }}', '{{ $d->jam_masuk ? date('H:i', strtotime($d->jam_masuk)) : '-' }}', '{{ $d->jam_pulang ? date('H:i', strtotime($d->jam_pulang)) : '-' }}', '{{ $statusText }}', '{{ $d->keterangan_izin ?? $d->keterangan_izin_sakit ?? $d->keterangan_izin_cuti ?? '' }}', '{{ !empty($d->foto_in) ? url('/storage/uploads/absensi/' . $d->foto_in) : '' }}', '{{ !empty($d->foto_out) ? url('/storage/uploads/absensi/' . $d->foto_out) : '' }}')">
                        <!-- Date Badge -->
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex flex-col items-center justify-center shrink-0 border border-slate-200/60 dark:border-slate-700">
                            <span class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase leading-none">{{ $day_short }}</span>
                            <span class="text-base font-extrabold text-slate-800 dark:text-slate-100 leading-none mt-0.5">{{ $tgl }}</span>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">{{ DateToIndo($d->tanggal) }}</h5>
                                </div>
                                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full border {{ $statusBadgeClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>

                            @if (!empty($d->nama_jam_kerja))
                                <div class="flex items-center gap-1 mt-0.5">
                                    <span class="inline-flex items-center text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                                        <i class="fa-solid fa-clock mr-1 text-[8.5px] text-blue-500"></i>
                                        {{ $d->nama_jam_kerja }}
                                        @if (!empty($d->jam_masuk) && !empty($d->jam_pulang))
                                            <span class="font-medium text-blue-500 dark:text-blue-400 ml-1">({{ date('H:i', strtotime($d->jam_masuk)) }} - {{ date('H:i', strtotime($d->jam_pulang)) }})</span>
                                        @endif
                                    </span>
                                </div>
                            @endif

                            @if ($d->status == 'h')
                                @php
                                    $jam_in_ts = strtotime($d->jam_in);
                                    $jam_masuk_ts = !empty($d->jam_masuk) ? strtotime($d->tanggal . ' ' . $d->jam_masuk) : null;
                                    $is_late = ($jam_masuk_ts && $jam_in_ts > $jam_masuk_ts);
                                @endphp
                                <div class="flex items-center justify-between mt-1 text-[11px]">
                                    <span class="font-bold text-slate-700 dark:text-slate-300">
                                        {{ $d->jam_in ? date('H:i', strtotime($d->jam_in)) : '--:--' }} - {{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : '--:--' }}
                                    </span>
                                    @if ($is_late)
                                        <span class="text-red-500 font-bold text-[10px] flex items-center gap-0.5">
                                            <i class="fa-solid fa-circle-exclamation text-[9px]"></i> Terlambat
                                        </span>
                                    @else
                                        <span class="text-teal-600 dark:text-teal-400 font-bold text-[10px] flex items-center gap-0.5">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i> Tepat Waktu
                                        </span>
                                    @endif
                                </div>
                            @elseif ($d->status == 'i')
                                <p class="text-[11px] text-sky-600 dark:text-sky-400 truncate mt-0.5">Izin: {{ $d->keterangan_izin }}</p>
                            @elseif ($d->status == 's')
                                <p class="text-[11px] text-amber-600 dark:text-amber-400 truncate mt-0.5">Sakit: {{ $d->keterangan_izin_sakit }}</p>
                            @elseif ($d->status == 'c')
                                <p class="text-[11px] text-purple-600 dark:text-purple-400 truncate mt-0.5">Cuti: {{ $d->keterangan_izin_cuti }}</p>
                            @else
                                <p class="text-[11px] text-red-600 dark:text-red-400 truncate mt-0.5">Alpha / Tanpa Keterangan</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        <i class="fa-solid fa-calendar-xmark text-2xl mb-1 text-slate-300 dark:text-slate-600 block"></i>
                        Belum ada riwayat presensi di bulan ini.
                    </div>
                @endforelse
            </div>

            <!-- Content Tab: Lembur -->
            <div id="contentLembur" class="space-y-2.5 hidden">
                @forelse ($lembur_presensi as $d)
                    @php
                        $tgl = date('d', strtotime($d->tanggal));
                        $day_short = strtoupper(date('D', strtotime($d->tanggal)));
                    @endphp
                    <div class="presence-card flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex flex-col items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-800">
                            <span class="text-[9px] font-bold uppercase leading-none">{{ $day_short }}</span>
                            <span class="text-base font-extrabold leading-none mt-0.5">{{ $tgl }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ DateToIndo($d->tanggal) }}</h5>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200">Lembur</span>
                            </div>
                            @if (!empty($d->nama_jam_kerja))
                                <div class="flex items-center gap-1 mt-0.5">
                                    <span class="inline-flex items-center text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                                        <i class="fa-solid fa-clock mr-1 text-[8.5px] text-blue-500"></i>
                                        {{ $d->nama_jam_kerja }}
                                    </span>
                                </div>
                            @endif
                            <div class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 mt-1">
                                {{ $d->jam_in ? date('H:i', strtotime($d->jam_in)) : '--:--' }} s/d {{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : '--:--' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        <i class="fa-solid fa-business-time text-2xl mb-1 text-slate-300 dark:text-slate-600 block"></i>
                        Belum ada data lembur di bulan ini.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ===== BOTTOM SHEET MODAL DETAIL PRESENSI ===== --}}
        <div id="detailPresensiModal" class="fixed inset-0 z-[999] flex items-end justify-center p-0" style="display:none;">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" onclick="hidePresensiDetail()"></div>
            <div class="relative bg-white dark:bg-slate-900 w-full max-w-md rounded-t-[32px] overflow-hidden shadow-2xl p-5 z-10 animate-slide-up max-h-[85vh] overflow-y-auto border-t border-slate-200 dark:border-slate-800">
                <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-4"></div>
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h4 id="detailTanggal" class="text-sm font-extrabold text-slate-800 dark:text-slate-100">-</h4>
                        <span id="detailShift" class="text-xs text-blue-600 dark:text-blue-400 font-semibold block mt-0.5">-</span>
                    </div>
                    <span id="detailStatusBadge" class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        Hadir
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 my-4">
                    <div class="bg-slate-50 dark:bg-slate-800/80 p-3 rounded-2xl border border-slate-100 dark:border-slate-700 text-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Jam Masuk</span>
                        <span id="detailJamIn" class="text-base font-extrabold text-slate-800 dark:text-slate-100 tracking-wide mt-0.5 block">-</span>
                        <div id="detailFotoInContainer" class="mt-2 w-full h-24 rounded-xl bg-slate-200 dark:bg-slate-700 overflow-hidden hidden">
                            <img id="detailFotoIn" src="" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/80 p-3 rounded-2xl border border-slate-100 dark:border-slate-700 text-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Jam Pulang</span>
                        <span id="detailJamOut" class="text-base font-extrabold text-slate-800 dark:text-slate-100 tracking-wide mt-0.5 block">-</span>
                        <div id="detailFotoOutContainer" class="mt-2 w-full h-24 rounded-xl bg-slate-200 dark:bg-slate-700 overflow-hidden hidden">
                            <img id="detailFotoOut" src="" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <div id="detailKeteranganContainer" class="mb-4 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700 text-xs hidden">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Catatan / Keterangan:</span>
                    <p id="detailKeterangan" class="text-slate-700 dark:text-slate-300 font-medium leading-relaxed"></p>
                </div>

                <button onclick="hidePresensiDetail()" class="w-full py-3 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 font-bold text-slate-700 dark:text-slate-300 active:scale-95 transition-all text-xs">
                    Tutup
                </button>
            </div>
        </div>

        {{-- ===== BIRTHDAY MODAL ===== --}}
        @if (isset($is_birthday) && $is_birthday)
            <div id="birthdayModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4" style="display:none;">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                <div class="relative rounded-[32px] w-full max-w-[340px] overflow-hidden shadow-2xl p-6 text-center text-white z-10"
                     style="background: linear-gradient(145deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #115e59 100%);">
                    <button onclick="hideBirthday()" class="absolute top-4 right-4 text-white/60 hover:text-white">
                        <i class="fa-solid fa-circle-xmark text-2xl"></i>
                    </button>
                    <div class="text-6xl mb-3 animate-bounce">🎂</div>
                    <h2 class="text-2xl font-black mb-1">Selamat Ulang Tahun!</h2>
                    <h3 class="text-lg font-bold text-white/90 mb-3">{{ $karyawan->nama_karyawan }}</h3>
                    @if ($umur)
                        <p class="text-xs text-white/80 mb-6 leading-relaxed">
                            Semoga di usia ke-<strong>{{ $umur }}</strong> tahun ini selalu diberikan kesehatan, kesuksesan, dan kebahagiaan. 🎉
                        </p>
                    @endif
                    <button onclick="hideBirthday()" class="w-full py-3 rounded-full bg-white font-bold text-teal-800 shadow-lg active:scale-95 transition-all">
                        Terima Kasih! 🙏
                    </button>
                </div>
            </div>
        @endif

        {{-- ===== DEVELOPER FOOTER ===== --}}
        <div class="text-center py-6 pb-24 text-[11px] text-slate-400 dark:text-slate-500">
            <span>SMATT V3 &bull; Dikembangkan oleh <a href="https://www.instagram.com/amn4ll/?utm_source=ig_web_button_share_sheet" target="_blank" class="text-slate-600 dark:text-slate-400 font-bold hover:underline">ZhanSoft - Amnal</a></span>
        </div>

        {{-- ===== BOTTOM NAVIGATION ===== --}}
        @include('layouts.mobile.bottomNav')

    </div>

    <!-- Scripts -->
    <script>
        // ===== THEME TOGGLE (DARK / LIGHT MODE) =====
        function initTheme() {
            var savedTheme = localStorage.getItem('smatt_theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark');
                updateThemeIcon(true);
            } else {
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('dark');
                updateThemeIcon(false);
            }
        }

        function toggleTheme() {
            var isDark = document.documentElement.classList.toggle('dark');
            document.body.classList.toggle('dark', isDark);
            localStorage.setItem('smatt_theme', isDark ? 'dark' : 'light');
            updateThemeIcon(isDark);
        }

        function updateThemeIcon(isDark) {
            var icon = document.getElementById('themeIcon');
            if (icon) {
                icon.setAttribute('name', isDark ? 'sunny-outline' : 'moon-outline');
            }
        }
        initTheme();

        // ===== DIGITAL CLOCK =====
        function updateClock() {
            var d = new Date();
            var h = d.getHours() < 10 ? '0' + d.getHours() : d.getHours();
            var m = d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes();
            var s = d.getSeconds() < 10 ? '0' + d.getSeconds() : d.getSeconds();
            var el = document.getElementById('jam');
            if (el) el.textContent = h + ':' + m + ':' + s;
            setTimeout(updateClock, 1000);
        }
        updateClock();

        // ===== REALTIME GPS OFFICE RADIUS CHECKER =====
        // ===== UNIFIED REALTIME GPS RADIUS & USER-LOCATION WEATHER =====
        var officeCoords = "{{ $karyawan->lokasi_cabang ?? '' }}".split(',');
        var officeRadius = parseInt("{{ $karyawan->radius_cabang ?? 50 }}") || 50;
        var isGpsChecking = false;

        function checkGpsRadius(isManual) {
            if (isGpsChecking) return;
            isGpsChecking = true;

            var refreshIcon = document.getElementById('weatherRefreshIcon');
            if (refreshIcon) refreshIcon.classList.add('animate-spin');

            var locNameEl = document.getElementById('userGpsLocationName');
            var statusTextEl = document.getElementById('gpsStatusText');
            if (locNameEl && isManual) locNameEl.textContent = 'Mendeteksi Posisi Anda...';
            if (statusTextEl && isManual) statusTextEl.textContent = 'Memeriksa radius kantor & cuaca...';

            if (!navigator.geolocation) {
                handleGpsError('Browser tidak mendukung GPS');
                return;
            }

            var officeLat = (officeCoords.length >= 2 && officeCoords[0]) ? parseFloat(officeCoords[0]) : null;
            var officeLon = (officeCoords.length >= 2 && officeCoords[1]) ? parseFloat(officeCoords[1]) : null;
            var isFlexible = (officeLat === null || officeLon === null);

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    var userLat = pos.coords.latitude;
                    var userLon = pos.coords.longitude;
                    var acc = pos.coords.accuracy ? Math.round(pos.coords.accuracy) : 10;
                    
                    var dist = 0;
                    var isInside = true;
                    if (!isFlexible) {
                        dist = getDistanceFromLatLonInM(userLat, userLon, officeLat, officeLon);
                        isInside = dist <= officeRadius;
                    }

                    updateGpsRadiusUI(isInside, isFlexible, dist, acc);
                    fetchUserLocationName(userLat, userLon);
                    fetchUserWeather(userLat, userLon, isInside, isFlexible, dist);
                },
                function(err) {
                    var errMsg = 'GPS nonaktif / Izin lokasi ditolak';
                    if (err.code === 2) errMsg = 'Posisi GPS tidak ditemukan';
                    if (err.code === 3) errMsg = 'Waktu permintaan GPS habis';
                    handleGpsError(errMsg);
                },
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 15000 }
            );
        }

        function updateGpsRadiusUI(isInside, isFlexible, dist, acc) {
            var iconBox = document.getElementById('gpsIconBox');
            var statusText = document.getElementById('gpsStatusText');
            var badge = document.getElementById('gpsBadge');
            var accEl = document.getElementById('gpsAccuracy');

            if (accEl) accEl.textContent = acc ? '±' + acc + ' m' : '-- m';

            if (isFlexible) {
                if (statusText) statusText.textContent = 'Lokasi Fleksibel (Bebas Radius)';
                if (iconBox) {
                    iconBox.className = 'w-8 h-8 rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300 flex items-center justify-center text-xs shrink-0 transition-all';
                    iconBox.innerHTML = '<i class="fa-solid fa-map-pin"></i>';
                }
                if (badge) {
                    badge.className = 'text-[10px] font-bold px-2 py-1 rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300 shrink-0';
                    badge.textContent = 'Fleksibel';
                }
            } else if (isInside) {
                if (statusText) statusText.textContent = 'Dalam Radius Kantor (~' + dist + 'm dari kantor)';
                if (iconBox) {
                    iconBox.className = 'w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 flex items-center justify-center text-xs shrink-0 transition-all';
                    iconBox.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
                }
                if (badge) {
                    badge.className = 'text-[10px] font-bold px-2 py-1 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 shrink-0';
                    badge.textContent = 'Valid';
                }
            } else {
                if (statusText) statusText.textContent = 'Di Luar Radius (~' + dist + 'm, Max: ' + officeRadius + 'm)';
                if (iconBox) {
                    iconBox.className = 'w-8 h-8 rounded-xl bg-red-100 text-red-600 dark:bg-red-900/60 dark:text-red-300 flex items-center justify-center text-xs shrink-0 transition-all';
                    iconBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
                }
                if (badge) {
                    badge.className = 'text-[10px] font-bold px-2 py-1 rounded-xl bg-red-100 text-red-600 dark:bg-red-900/60 dark:text-red-300 shrink-0';
                    badge.textContent = 'Luar Radius';
                }
            }
        }

        function fetchUserLocationName(lat, lon) {
            var geocodeUrl = 'https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=' + lat + '&longitude=' + lon + '&localityLanguage=id';
            fetch(geocodeUrl)
                .then(function(res) { return res.json(); })
                .then(function(geo) {
                    var place = 'Lokasi Terdeteksi';
                    if (geo.locality && geo.city) {
                        place = geo.locality + ', ' + geo.city;
                    } else if (geo.locality || geo.city) {
                        place = geo.locality || geo.city;
                    } else if (geo.principalSubdivision) {
                        place = geo.principalSubdivision;
                    }
                    var locEl = document.getElementById('userGpsLocationName');
                    if (locEl) locEl.textContent = '📍 ' + place;
                })
                .catch(function() {
                    var locEl = document.getElementById('userGpsLocationName');
                    if (locEl) locEl.textContent = '📍 Titik GPS (' + lat.toFixed(3) + ', ' + lon.toFixed(3) + ')';
                });
        }

        function fetchUserWeather(lat, lon, isInside, isFlexible, dist) {
            var weatherUrl = 'https://api.open-meteo.com/v1/forecast?latitude=' + lat + '&longitude=' + lon + '&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m&timezone=auto';
            fetch(weatherUrl)
                .then(function(res) {
                    if (!res.ok) throw new Error('Weather API error');
                    return res.json();
                })
                .then(function(data) {
                    if (data && data.current) {
                        renderUserWeatherUI(data.current, isInside, isFlexible, dist);
                    }
                })
                .catch(function() {
                    var descEl = document.getElementById('weatherConditionDesc');
                    if (descEl) descEl.textContent = 'Cuaca offline';
                    updateUnifiedAdvisory('Kondisi Kerja Lapangan', 'Patuhi prosedur kerja dan tetap berhati-hati dalam bertugas.', 'info');
                })
                .finally(function() {
                    stopRefreshAnimation();
                });
        }

        function renderUserWeatherUI(current, isInside, isFlexible, dist) {
            var temp = Math.round(current.temperature_2m);
            var humidity = Math.round(current.relative_humidity_2m);
            var wind = Math.round(current.wind_speed_10m);
            var code = current.weather_code;

            var tempEl = document.getElementById('weatherTemp');
            var descEl = document.getElementById('weatherConditionDesc');
            var envEl = document.getElementById('weatherEnvStats');
            var mainIcon = document.getElementById('weatherMainIcon');

            if (tempEl) tempEl.textContent = temp + '°C';
            if (envEl) envEl.textContent = humidity + '% / ' + wind + ' km/j';

            var weatherMeta = getWeatherInterpretation(code, temp);
            if (descEl) descEl.textContent = weatherMeta.desc;
            if (mainIcon) mainIcon.className = weatherMeta.icon + ' text-xs';

            // Generate Combined Unified Advisory
            var title = 'Rekomendasi Lapangan & Cuaca';
            var msg = '';
            var type = 'success';

            if (weatherMeta.type === 'thunderstorm') {
                title = 'Peringatan Badai Petir di Titik Anda';
                msg = 'Waspada petir & angin kencang di sekitar posisi Anda. Hindari area terbuka saat badai.';
                type = 'danger';
            } else if (weatherMeta.type === 'rain' || weatherMeta.type === 'showers') {
                title = 'Peringatan Hujan di Titik Anda';
                msg = 'Hujan terdeteksi di lokasi Anda. Bawa payung/jas hujan & waspadai jalan licin saat perjalanan.';
                type = 'warning';
            } else if (weatherMeta.type === 'drizzle') {
                title = 'Info Cuaca Lapangan (Gerimis)';
                msg = 'Gerimis turun di sekitar titik Anda. Berhati-hati saat berkendara & amankan berkas kerja.';
                type = 'warning';
            } else if (temp >= 34) {
                title = 'Suhu Udara Terik (' + temp + '°C)';
                msg = 'Suhu lingkungan cukup panas. Pastikan cukup minum dan lindungi diri dari paparan panas terik.';
                type = 'warning';
            } else {
                if (!isFlexible && !isInside) {
                    title = 'Aktivitas Luar Radius Kantor';
                    msg = 'Cuaca ' + weatherMeta.desc.toLowerCase() + ' (' + temp + '°C). Anda berada di luar radius kantor (~' + dist + 'm).';
                    type = 'info';
                } else {
                    title = 'Lokasi Valid & Cuaca Mendukung';
                    msg = 'Radius kantor valid & cuaca ' + weatherMeta.desc.toLowerCase() + ' (' + temp + '°C). Kondisi optimal untuk beraktivitas.';
                    type = 'success';
                }
            }

            updateUnifiedAdvisory(title, msg, type);
        }

        function getWeatherInterpretation(code, temp) {
            if (code === 0) {
                return { desc: 'Cerah Berawan', icon: 'fa-solid fa-sun text-amber-500', type: 'sunny' };
            } else if (code === 1 || code === 2) {
                return { desc: 'Cerah Berawan', icon: 'fa-solid fa-cloud-sun text-amber-400', type: 'partly_cloudy' };
            } else if (code === 3) {
                return { desc: 'Mendung Berawan', icon: 'fa-solid fa-cloud text-slate-400', type: 'cloudy' };
            } else if (code === 45 || code === 48) {
                return { desc: 'Udara Berkabut', icon: 'fa-solid fa-smog text-slate-400', type: 'fog' };
            } else if (code >= 51 && code <= 55) {
                return { desc: 'Gerimis Ringan', icon: 'fa-solid fa-cloud-rain text-sky-400', type: 'drizzle' };
            } else if (code >= 61 && code <= 65) {
                return { desc: 'Hujan Deras', icon: 'fa-solid fa-cloud-showers-heavy text-blue-500', type: 'rain' };
            } else if (code >= 80 && code <= 82) {
                return { desc: 'Hujan Lokal', icon: 'fa-solid fa-cloud-sun-rain text-blue-400', type: 'showers' };
            } else if (code >= 95) {
                return { desc: 'Badai Petir', icon: 'fa-solid fa-cloud-bolt text-purple-500', type: 'thunderstorm' };
            } else {
                return { desc: 'Berawan Normal', icon: 'fa-solid fa-cloud text-teal-500', type: 'normal' };
            }
        }

        function updateUnifiedAdvisory(title, msg, type) {
            var box = document.getElementById('weatherAdvisoryBox');
            var icon = document.getElementById('weatherAdvisoryIcon');
            var textEl = document.getElementById('weatherAdvisoryText');

            if (!box || !icon || !textEl) return;

            textEl.innerHTML = '<strong>' + title + ':</strong> ' + msg;

            if (type === 'danger') {
                box.className = 'mt-2 p-2 rounded-xl bg-red-50/90 dark:bg-red-950/40 border border-red-200 dark:border-red-800/60 flex items-start gap-2 transition-all';
                icon.className = 'w-5 h-5 rounded-md bg-red-500 text-white flex items-center justify-center text-[9px] shrink-0 mt-0.5';
                icon.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
                textEl.className = 'text-[10.5px] text-red-900 dark:text-red-200 font-medium leading-snug';
            } else if (type === 'warning') {
                box.className = 'mt-2 p-2 rounded-xl bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 flex items-start gap-2 transition-all';
                icon.className = 'w-5 h-5 rounded-md bg-amber-500 text-white flex items-center justify-center text-[9px] shrink-0 mt-0.5';
                icon.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i>';
                textEl.className = 'text-[10.5px] text-amber-900 dark:text-amber-200 font-medium leading-snug';
            } else if (type === 'info') {
                box.className = 'mt-2 p-2 rounded-xl bg-sky-50/90 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800/60 flex items-start gap-2 transition-all';
                icon.className = 'w-5 h-5 rounded-md bg-sky-500 text-white flex items-center justify-center text-[9px] shrink-0 mt-0.5';
                icon.innerHTML = '<i class="fa-solid fa-circle-info"></i>';
                textEl.className = 'text-[10.5px] text-sky-900 dark:text-sky-200 font-medium leading-snug';
            } else {
                box.className = 'mt-2 p-2 rounded-xl bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 flex items-start gap-2 transition-all';
                icon.className = 'w-5 h-5 rounded-md bg-emerald-500 text-white flex items-center justify-center text-[9px] shrink-0 mt-0.5';
                icon.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
                textEl.className = 'text-[10.5px] text-emerald-900 dark:text-emerald-200 font-medium leading-snug';
            }
        }

        function handleGpsError(errMsg) {
            var iconBox = document.getElementById('gpsIconBox');
            var locName = document.getElementById('userGpsLocationName');
            var statusText = document.getElementById('gpsStatusText');
            var badge = document.getElementById('gpsBadge');
            var tempEl = document.getElementById('weatherTemp');
            var descEl = document.getElementById('weatherConditionDesc');
            var envEl = document.getElementById('weatherEnvStats');
            var accEl = document.getElementById('gpsAccuracy');

            if (locName) locName.textContent = '📍 GPS Tidak Aktif';
            if (statusText) statusText.textContent = errMsg;
            if (tempEl) tempEl.textContent = '--°C';
            if (descEl) descEl.textContent = 'GPS Diperlukan';
            if (envEl) envEl.textContent = '-- / --';
            if (accEl) accEl.textContent = '-- m';

            if (iconBox) {
                iconBox.className = 'w-8 h-8 rounded-xl bg-red-100 text-red-600 dark:bg-red-900/60 dark:text-red-300 flex items-center justify-center text-xs shrink-0 transition-all';
                iconBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
            }
            if (badge) {
                badge.className = 'text-[10px] font-bold px-2 py-1 rounded-xl bg-red-100 text-red-600 dark:bg-red-900/60 dark:text-red-300 shrink-0';
                badge.textContent = 'GPS Off';
            }

            updateUnifiedAdvisory('Izin Lokasi (GPS) Diperlukan', 'Aktifkan GPS pada perangkat / browser agar sistem dapat memvalidasi radius kantor dan mendeteksi cuaca di titik Anda.', 'warning');
            stopRefreshAnimation();
        }

        function stopRefreshAnimation() {
            isGpsChecking = false;
            var refreshIcon = document.getElementById('weatherRefreshIcon');
            if (refreshIcon) {
                setTimeout(function() {
                    refreshIcon.classList.remove('animate-spin');
                }, 400);
            }
        }

        function getDistanceFromLatLonInM(lat1, lon1, lat2, lon2) {
            var R = 6371000;
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLon = (lon2 - lon1) * Math.PI / 180;
            var a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return Math.round(R * c);
        }

        checkGpsRadius();

        // ===== LIVE WORK DURATION TIMER =====
        @if (!empty($presensi->jam_in) && empty($presensi->jam_out))
            var jamInTime = new Date("{{ date('Y-m-d') }}T{{ date('H:i:s', strtotime($presensi->jam_in)) }}").getTime();
            function updateWorkDuration() {
                var now = new Date().getTime();
                var diff = Math.max(0, now - jamInTime);
                var hours = Math.floor(diff / (1000 * 60 * 60));
                var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((diff % (1000 * 60)) / 1000);

                var formatted = (hours < 10 ? '0' + hours : hours) + 'j ' +
                                (minutes < 10 ? '0' + minutes : minutes) + 'm ' +
                                (seconds < 10 ? '0' + seconds : seconds) + 'd';

                var el = document.getElementById('liveWorkDuration');
                if (el) el.textContent = formatted;
                setTimeout(updateWorkDuration, 1000);
            }
            updateWorkDuration();
        @endif

        $(document).ready(function() {
            var track = $('.carousel-track');
            var slides = $('.alert-slide');
            var dots = $('.dot');
            if (slides.length > 1) {
                var current = 0;
                setInterval(function() {
                    current = (current + 1) % slides.length;
                    track.css('transform', 'translateX(-' + (current * 100) + '%)');
                    dots.removeClass('active');
                    $(dots[current]).addClass('active');
                }, 5000);
            }

            @if (isset($is_birthday) && $is_birthday)
                setTimeout(function(){
                    $('#birthdayModal').fadeIn(300);
                }, 1200);
            @endif
        });

        function hideBirthday() {
            $('#birthdayModal').fadeOut(250);
        }

        function switchTab(tab) {
            if (tab === 'presensi') {
                $('#contentPresensi').removeClass('hidden');
                $('#contentLembur').addClass('hidden');
                $('#tabPresensi').addClass('bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-sm').removeClass('text-slate-500 dark:text-slate-400');
                $('#tabLembur').removeClass('bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-sm').addClass('text-slate-500 dark:text-slate-400');
            } else {
                $('#contentPresensi').addClass('hidden');
                $('#contentLembur').removeClass('hidden');
                $('#tabLembur').addClass('bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-sm').removeClass('text-slate-500 dark:text-slate-400');
                $('#tabPresensi').removeClass('bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-sm').addClass('text-slate-500 dark:text-slate-400');
            }
        }

        // ===== DETAIL BOTTOM SHEET =====
        function showPresensiDetail(tanggal, tanggalIndo, shiftName, jamIn, jamOut, jadwalMasuk, jadwalPulang, status, keterangan, fotoIn, fotoOut) {
            $('#detailTanggal').text(tanggalIndo);
            $('#detailShift').text(shiftName !== '-' ? 'Shift: ' + shiftName + ' (' + jadwalMasuk + ' - ' + jadwalPulang + ')' : 'Jadwal Fleksibel');
            $('#detailStatusBadge').text(status);
            $('#detailJamIn').text(jamIn);
            $('#detailJamOut').text(jamOut);

            if (fotoIn) {
                $('#detailFotoIn').attr('src', fotoIn);
                $('#detailFotoInContainer').removeClass('hidden');
            } else {
                $('#detailFotoInContainer').addClass('hidden');
            }

            if (fotoOut) {
                $('#detailFotoOut').attr('src', fotoOut);
                $('#detailFotoOutContainer').removeClass('hidden');
            } else {
                $('#detailFotoOutContainer').addClass('hidden');
            }

            if (keterangan && keterangan.trim() !== '') {
                $('#detailKeterangan').text(keterangan);
                $('#detailKeteranganContainer').removeClass('hidden');
            } else {
                $('#detailKeteranganContainer').addClass('hidden');
            }

            $('#detailPresensiModal').fadeIn(200);
        }

        function hidePresensiDetail() {
            $('#detailPresensiModal').fadeOut(200);
        }

        $("#btnDaftarkanWajah").click(function(e) {
            e.preventDefault();
            window.location.href = "{{ route('facerecognition.karyawan.create') }}";
        });
    </script>

    <!-- WebPush Manager and Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('WebPush: Service Worker registered'))
                .catch(err => console.error('WebPush: Service Worker registration failed', err));
        }
    </script>
    <script src="{{ asset('assets/template/js/webpush.js') }}?v={{ time() }}"></script>

    @include('components.push-notification-banner')
</body>
</html>
