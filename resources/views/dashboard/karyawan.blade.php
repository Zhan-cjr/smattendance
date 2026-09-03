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
        }

        .hero-gradient {
            background: linear-gradient(145deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #115e59 50%, #042f2e 100%);
            border-bottom-left-radius: 36px;
            border-bottom-right-radius: 36px;
            position: relative;
            box-shadow: 0 14px 35px -8px rgba(15, 118, 110, 0.35);
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

        .carousel-wrapper { width: 100%; overflow: hidden; position: relative; border-radius: 20px; }
        .carousel-track { display: flex; transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1); width: 100%; }
        .carousel-track .alert-slide { width: 100%; flex: 0 0 100%; flex-shrink: 0; box-sizing: border-box; }

        /* Modern Action Card */
        .action-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.9);
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
        .app-tile:active {
            transform: scale(0.93);
            background: #f8fafc;
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
            transition: transform 0.15s ease;
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
                <a href="{{ route('karyawan-approval.index') }}" class="glass-btn relative">
                    <ion-icon name="notifications-outline" style="font-size:22px;"></ion-icon>
                    @if (isset($pendingApprovalCount) && $pendingApprovalCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 rounded-full border-2 border-teal-800 text-[10px] font-extrabold flex items-center justify-center px-1">
                            {{ $pendingApprovalCount }}
                        </span>
                    @endif
                </a>
                
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-semibold bg-white/15 px-3 py-1 rounded-full backdrop-blur-md border border-white/20">
                        {{ $karyawan->nama_cabang ?? 'Pusat' }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="glass-btn">
                            <ion-icon name="log-out-outline" style="font-size:22px;"></ion-icon>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex-1 min-w-0 pr-3">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 text-white/90 text-[11px] font-medium mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>{{ $karyawan->nama_jabatan }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-white truncate leading-tight">
                        {{ $karyawan->nama_karyawan }} 👋
                    </h3>
                    <p class="text-xs text-white/70 truncate mt-0.5">
                        NIK: {{ $karyawan->nik }} &bull; Dept: {{ $karyawan->nama_dept }}
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
            </div>
        </div>

        {{-- ===== FLOATING ATTENDANCE ACTION CARD ===== --}}
        <div class="px-4 -mt-10 relative z-20">
            <div class="action-card p-4">
                <!-- Status Badge -->
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Status Hari Ini</span>
                        @if (!empty($presensi->jam_out))
                            <span class="text-xs font-bold text-blue-600 flex items-center gap-1 mt-0.5">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Selesai Bekerja (Pulang)
                            </span>
                        @elseif (!empty($presensi->jam_in))
                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1 mt-0.5">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Sudah Masuk Kerja
                            </span>
                        @else
                            <span class="text-xs font-bold text-amber-500 flex items-center gap-1 mt-0.5">
                                <i class="fa-solid fa-clock text-[10px]"></i> Belum Melakukan Presensi
                            </span>
                        @endif
                    </div>

                    <a href="/presensi/create" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-white shadow-md active:scale-95 transition-all"
                       style="background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #0d9488 100%);">
                        <ion-icon name="finger-print" style="font-size:16px;"></ion-icon>
                        <span>Absen Sekarang</span>
                    </a>
                </div>

                <!-- Jam In & Jam Out Details -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Jam Masuk -->
                    <div class="bg-slate-50 p-2.5 rounded-2xl flex items-center gap-2.5 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden relative group">
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
                            <span class="text-sm font-extrabold text-slate-800 tracking-wide">
                                {{ !empty($presensi->jam_in) ? date('H:i', strtotime($presensi->jam_in)) : '--:--' }}
                            </span>
                        </div>
                    </div>

                    <!-- Jam Pulang -->
                    <div class="bg-slate-50 p-2.5 rounded-2xl flex items-center gap-2.5 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden relative group">
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
                            <span class="text-sm font-extrabold text-slate-800 tracking-wide">
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
                                <a href="{{ route('pengumuman.show', Crypt::encrypt($pengumuman->id)) }}" class="alert-slide block p-3.5 rounded-2xl bg-sky-50 border border-sky-200 text-decoration-none hover:bg-sky-100/60 active:scale-[0.99] transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-sky-100 text-sky-600">
                                            <i class="fa-solid fa-bullhorn text-base"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1">
                                                <h5 class="text-xs font-bold text-sky-900 truncate">{{ $pengumuman->judul }}</h5>
                                                <span class="text-[10px] text-sky-500 font-medium shrink-0">{{ \Carbon\Carbon::parse($pengumuman->created_at)->translatedFormat('d M Y') }}</span>
                                            </div>
                                            <div class="text-[11px] text-sky-700 mt-1 line-clamp-2 leading-snug">{!! strip_tags($pengumuman->isi) !!}</div>
                                            <div class="mt-2 flex items-center justify-end">
                                                <span class="text-[11px] font-bold text-sky-800 flex items-center gap-1 hover:underline">
                                                    <span>Baca Selengkapnya</span>
                                                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @elseif($type == 'kontrak')
                                <a href="{{ route('kontrak.index') }}" class="alert-slide block p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-decoration-none hover:bg-amber-100/60 active:scale-[0.99] transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-amber-100 text-amber-600">
                                            <i class="fa-solid fa-file-contract text-base"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h5 class="text-xs font-bold text-amber-900">Masa Kontrak Berakhir Segera</h5>
                                            <p class="text-[11px] text-amber-700 mt-0.5 leading-snug">
                                                Sisa masa kontrak: <strong>{{ $notif_kontrak['sisa_hari'] }} hari</strong> (Selesai: {{ $notif_kontrak['tanggal_akhir'] }}).
                                            </p>
                                            <div class="mt-2 flex items-center justify-end">
                                                <span class="text-[11px] font-bold text-amber-800 flex items-center gap-1 hover:underline">
                                                    <span>Lihat Detail Kontrak</span>
                                                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @elseif($type == 'sp')
                                <a href="{{ route('pelanggaran.index') }}" class="alert-slide block p-3.5 rounded-2xl bg-red-50 border border-red-200 text-decoration-none hover:bg-red-100/60 active:scale-[0.99] transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-red-100 text-red-600">
                                            <i class="fa-solid fa-triangle-exclamation text-base"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h5 class="text-xs font-bold text-red-900">Peringatan Disiplin Aktif</h5>
                                            <p class="text-[11px] text-red-700 mt-0.5 leading-snug">
                                                {{ $notif_sp->jenis_sp }} s/d {{ \Carbon\Carbon::parse($notif_sp->sampai)->translatedFormat('d M Y') }}.
                                            </p>
                                            <div class="mt-2 flex items-center justify-end">
                                                <span class="text-[11px] font-bold text-red-800 flex items-center gap-1 hover:underline">
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

        {{-- ===== BENTO MONTHLY RECAP ===== --}}
        <div class="px-4 mt-4">
            <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Rekap Bulan {{ $bulan_skrg }}</h4>
                        <span class="text-[10px] text-slate-400">Total akumulasi presensi Anda</span>
                    </div>
                    <span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">
                        {{ date('H:i') }} WIB
                    </span>
                </div>

                <div class="grid grid-cols-5 gap-2 text-center">
                    <!-- Hadir -->
                    <div class="bg-teal-50/70 p-2.5 rounded-2xl border border-teal-100/50">
                        <span class="text-lg font-black text-teal-700 block leading-tight">{{ $rekappresensi->hadir ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-teal-600 block mt-0.5">Hadir</span>
                    </div>

                    <!-- Lembur -->
                    <div class="bg-emerald-50/70 p-2.5 rounded-2xl border border-emerald-100/50">
                        <span class="text-lg font-black text-emerald-700 block leading-tight">{{ $rekappresensi->lembur ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-emerald-600 block mt-0.5">Lembur</span>
                    </div>

                    <!-- Sakit -->
                    <div class="bg-amber-50/70 p-2.5 rounded-2xl border border-amber-100/50">
                        <span class="text-lg font-black text-amber-700 block leading-tight">{{ $rekappresensi->sakit ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-amber-600 block mt-0.5">Sakit</span>
                    </div>

                    <!-- Izin -->
                    <div class="bg-sky-50/70 p-2.5 rounded-2xl border border-sky-100/50">
                        <span class="text-lg font-black text-sky-700 block leading-tight">{{ $rekappresensi->izin ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-sky-600 block mt-0.5">Izin</span>
                    </div>

                    <!-- Cuti -->
                    <div class="bg-purple-50/70 p-2.5 rounded-2xl border border-purple-100/50">
                        <span class="text-lg font-black text-purple-700 block leading-tight">{{ $rekappresensi->cuti ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-purple-600 block mt-0.5">Cuti</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== QUICK APP GRID (8 APPS) ===== --}}
        <div class="px-4 mt-4">
            <div class="grid grid-cols-4 gap-2.5">
                <!-- 1. ID Card -->
                <a href="{{ route('karyawan.idcard', Crypt::encrypt($karyawan->nik)) }}" class="app-tile">
                    <div class="app-icon-box bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-700">ID Card</span>
                </a>

                <!-- 2. Istirahat / Kontrak -->
                @if ($general_setting->absen_istirahat == 1)
                    <a href="{{ route('presensiistirahat.create') }}" class="app-tile">
                        <div class="app-icon-box bg-amber-50 text-amber-600">
                            <i class="fa-solid fa-mug-hot"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700">Istirahat</span>
                    </a>
                @else
                    <a href="{{ route('kontrak.index') }}" class="app-tile">
                        <div class="app-icon-box bg-amber-50 text-amber-600">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700">Kontrak</span>
                    </a>
                @endif

                <!-- 3. Lembur -->
                <a href="{{ route('presensiistirahatlembur.create') }}" class="app-tile">
                    <div class="app-icon-box bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-business-time"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-700">Lembur</span>
                </a>

                <!-- 4. Slip Gaji -->
                <a href="{{ route('slipgaji.index') }}" class="app-tile">
                    <div class="app-icon-box bg-teal-50 text-teal-600">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-700">Slip Gaji</span>
                </a>

                <!-- 5. Aktivitas -->
                @can('aktivitaskaryawan.index')
                    <a href="{{ route('aktivitaskaryawan.index') }}" class="app-tile">
                        <div class="app-icon-box bg-indigo-50 text-indigo-600">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700">Aktivitas</span>
                    </a>
                @endcan

                <!-- 6. Visit / Kunjungan -->
                @can('kunjungan.index')
                    <a href="{{ route('kunjungan.index') }}" class="app-tile">
                        <div class="app-icon-box bg-rose-50 text-rose-600">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-700">Visit</span>
                    </a>
                @endcan

                <!-- 7. Scan Wajah -->
                <a href="javascript:void(0)" id="btnDaftarkanWajah" class="app-tile">
                    <div class="app-icon-box bg-purple-50 text-purple-600">
                        <i class="fa-solid fa-user-astronaut"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-700">Wajah</span>
                </a>

                <!-- 8. Lainnya -->
                <a href="{{ route('shortcut.index') }}" class="app-tile">
                    <div class="app-icon-box bg-slate-100 text-slate-700">
                        <i class="fa-solid fa-ellipsis"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-700">Lainnya</span>
                </a>
            </div>
        </div>

        {{-- ===== ATTENDANCE HISTORY TABS ===== --}}
        <div class="px-4 mt-5">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-extrabold text-slate-800">Riwayat Kehadiran</h4>
                <div class="flex p-1 bg-slate-200/70 rounded-full text-xs font-semibold">
                    <button id="tabPresensi" onclick="switchTab('presensi')" class="px-3 py-1 rounded-full bg-white text-slate-800 shadow-sm transition-all">
                        Presensi
                    </button>
                    <button id="tabLembur" onclick="switchTab('lembur')" class="px-3 py-1 rounded-full text-slate-500 transition-all">
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

                        $statusBadgeClass = 'bg-teal-50 text-teal-700 border-teal-200';
                        $statusText = 'Hadir';
                        if ($d->status == 'i') { $statusBadgeClass = 'bg-sky-50 text-sky-700 border-sky-200'; $statusText = 'Izin'; }
                        elseif ($d->status == 's') { $statusBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200'; $statusText = 'Sakit'; }
                        elseif ($d->status == 'c') { $statusBadgeClass = 'bg-purple-50 text-purple-700 border-purple-200'; $statusText = 'Cuti'; }
                        elseif ($d->status == 'a') { $statusBadgeClass = 'bg-red-50 text-red-700 border-red-200'; $statusText = 'Alpha'; }
                    @endphp

                    <div class="presence-card flex items-center gap-3">
                        <!-- Date Badge -->
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex flex-col items-center justify-center shrink-0 border border-slate-200/60">
                            <span class="text-[9px] font-bold text-slate-500 uppercase leading-none">{{ $day_short }}</span>
                            <span class="text-base font-extrabold text-slate-800 leading-none mt-0.5">{{ $tgl }}</span>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <h5 class="text-xs font-bold text-slate-800 truncate">{{ DateToIndo($d->tanggal) }}</h5>
                                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full border {{ $statusBadgeClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>

                            @if ($d->status == 'h')
                                @php
                                    $jam_in_ts = strtotime($d->jam_in);
                                    $jam_masuk_ts = strtotime($d->tanggal . ' ' . $d->jam_masuk);
                                    $is_late = $jam_in_ts > $jam_masuk_ts;
                                @endphp
                                <div class="flex items-center justify-between mt-1 text-[11px]">
                                    <span class="font-bold text-slate-700">
                                        {{ $d->jam_in ? date('H:i', strtotime($d->jam_in)) : '--:--' }} - {{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : '--:--' }}
                                    </span>
                                    @if ($is_late)
                                        <span class="text-red-500 font-bold text-[10px]">Terlambat</span>
                                    @else
                                        <span class="text-teal-600 font-bold text-[10px]">Tepat Waktu</span>
                                    @endif
                                </div>
                            @elseif ($d->status == 'i')
                                <p class="text-[11px] text-sky-600 truncate mt-0.5">Izin: {{ $d->keterangan_izin }}</p>
                            @elseif ($d->status == 's')
                                <p class="text-[11px] text-amber-600 truncate mt-0.5">Sakit: {{ $d->keterangan_izin_sakit }}</p>
                            @elseif ($d->status == 'c')
                                <p class="text-[11px] text-purple-600 truncate mt-0.5">Cuti: {{ $d->keterangan_izin_cuti }}</p>
                            @else
                                <p class="text-[11px] text-red-600 truncate mt-0.5">Alpha / Tanpa Keterangan</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        <i class="fa-solid fa-calendar-xmark text-2xl mb-1 text-slate-300 block"></i>
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
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex flex-col items-center justify-center shrink-0 border border-emerald-100">
                            <span class="text-[9px] font-bold uppercase leading-none">{{ $day_short }}</span>
                            <span class="text-base font-extrabold leading-none mt-0.5">{{ $tgl }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h5 class="text-xs font-bold text-slate-800">{{ DateToIndo($d->tanggal) }}</h5>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Lembur</span>
                            </div>
                            <div class="text-[11px] font-semibold text-slate-600 mt-1">
                                {{ $d->jam_in ? date('H:i', strtotime($d->jam_in)) : '--:--' }} s/d {{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : '--:--' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        <i class="fa-solid fa-business-time text-2xl mb-1 text-slate-300 block"></i>
                        Belum ada data lembur di bulan ini.
                    </div>
                @endforelse
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
        <div class="text-center py-6 pb-24 text-[11px] text-slate-400">
            <span>SMATT V3 &bull; Dikembangkan oleh <a href="https://www.instagram.com/amn4ll/?utm_source=ig_web_button_share_sheet" target="_blank" class="text-slate-600 font-bold hover:underline">ZhanSoft - Amnal</a></span>
        </div>

        {{-- ===== BOTTOM NAVIGATION ===== --}}
        @include('layouts.mobile.bottomNav')

    </div>

    <!-- Scripts -->
    <script>
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
                $('#tabPresensi').addClass('bg-white text-slate-800 shadow-sm').removeClass('text-slate-500');
                $('#tabLembur').removeClass('bg-white text-slate-800 shadow-sm').addClass('text-slate-500');
            } else {
                $('#contentPresensi').addClass('hidden');
                $('#contentLembur').removeClass('hidden');
                $('#tabLembur').addClass('bg-white text-slate-800 shadow-sm').removeClass('text-slate-500');
                $('#tabPresensi').removeClass('bg-white text-slate-800 shadow-sm').addClass('text-slate-500');
            }
        }

        $("#btnDaftarkanWajah").click(function(e) {
            e.preventDefault();
            window.location.href = "{{ route('facerecognition.karyawan.create') }}";
        });
    </script>
</body>
</html>
