@extends('layouts.mobile.modern')
@section('title')
    <div class="text-center leading-tight">
        <div class="font-extrabold text-[15px] tracking-tight">Pilih Jam Kerja</div>
        <div class="text-[9.5px] font-medium opacity-75">SM-Attendance</div>
    </div>
@endsection

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@section('header_right')
    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white text-xs font-bold">
        <ion-icon name="time-outline" class="text-lg"></ion-icon>
    </div>
@endsection

@push('mystyle')
    <script>
        (function() {
            var serverScheme = "{{ $general_setting->mobile_theme_scheme ?? 'green' }}";
            var lastServerScheme = localStorage.getItem('smatt_server_scheme');
            if (lastServerScheme !== serverScheme) {
                localStorage.setItem('smatt_server_scheme', serverScheme);
                localStorage.setItem('smatt_theme', serverScheme === 'dark' ? 'dark' : 'light');
            }
            var savedTheme = localStorage.getItem('smatt_theme') || (serverScheme === 'dark' ? 'dark' : 'light');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
                if (document.body) document.body.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark', 'dark-mode-active');
                if (document.body) document.body.classList.remove('dark', 'dark-mode-active');
            }
        })();
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --theme-primary: {{ $t['primary'] ?? '#32745e' }};
            --theme-light: {{ $t['primary_light'] ?? '#58907D' }};
            --theme-dark: {{ $t['primary_dark'] ?? '#1e4b3d' }};
            --theme-rgb: {{ $t['primary_rgb'] ?? '50, 116, 94' }};
            --theme-bg: {{ $t['bg_body'] ?? '#f8fafc' }};
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: var(--theme-bg) !important;
            color: #0f172a !important;
        }

        /* ===== Hero Banner ===== */
        .shift-hero-banner {
            background: linear-gradient(135deg, var(--theme-light) 0%, var(--theme-primary) 50%, var(--theme-dark) 100%);
            border-radius: 24px;
            box-shadow: 0 12px 28px -6px rgba(var(--theme-rgb), 0.38);
            border: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            overflow: hidden;
        }

        /* ===== Shift Card Bento Styling ===== */
        .shift-card {
            background: #ffffff !important;
            border-radius: 22px !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 6px 20px -4px rgba(15, 23, 42, 0.05) !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .shift-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4.5px;
            background: linear-gradient(180deg, var(--theme-light) 0%, var(--theme-primary) 100%);
            opacity: 0.35;
            transition: opacity 0.25s ease, width 0.25s ease;
        }

        .shift-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -6px rgba(var(--theme-rgb), 0.18) !important;
            border-color: rgba(var(--theme-rgb), 0.55) !important;
        }

        .shift-card:hover::before {
            opacity: 1;
            width: 5.5px;
        }

        .shift-card:active {
            transform: scale(0.985);
        }

        .shift-card.active-shift-recommended {
            border-color: rgba(var(--theme-rgb), 0.45) !important;
            box-shadow: 0 10px 26px -6px rgba(var(--theme-rgb), 0.18) !important;
        }

        .shift-card.active-shift-recommended::before {
            opacity: 1;
            width: 5px;
        }

        .shift-recommended-badge {
            background: rgba(var(--theme-rgb), 0.12);
            color: var(--theme-primary);
            border: 1px solid rgba(var(--theme-rgb), 0.28);
            font-size: 10px;
            font-weight: 800;
            padding: 3px 9px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .shift-title-text {
            color: #0f172a !important;
            font-weight: 800 !important;
            font-size: 14px !important;
            line-height: 1.25 !important;
            letter-spacing: -0.01em;
        }

        /* ===== Connected Timeline Bar ===== */
        .shift-timeline-box {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            position: relative;
        }

        .shift-time-endpoint {
            display: flex;
            flex-direction: column;
            min-width: 78px;
        }

        .shift-time-endpoint.end-right {
            text-align: right;
            align-items: flex-end;
        }

        .shift-time-label-text {
            font-size: 9.5px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        .shift-time-val-text {
            font-size: 17px !important;
            font-weight: 900 !important;
            color: #0f172a !important;
            letter-spacing: 0.02em !important;
            line-height: 1.2 !important;
            margin-top: 2px !important;
            font-variant-numeric: tabular-nums;
        }

        .shift-timeline-connector {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }

        .shift-timeline-line {
            width: 100%;
            height: 2px;
            background: repeating-linear-gradient(
                to right,
                #cbd5e1 0,
                #cbd5e1 4px,
                transparent 4px,
                transparent 8px
            );
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .shift-timeline-pill {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: var(--theme-primary);
            font-size: 9.5px;
            font-weight: 800;
            padding: 1.5px 8px;
            border-radius: 9999px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
            white-space: nowrap;
        }

        /* ===== Break Info Banner ===== */
        .shift-break-banner {
            background: rgba(245, 158, 11, 0.08) !important;
            border: 1px dashed rgba(245, 158, 11, 0.35) !important;
            border-radius: 12px !important;
            padding: 6px 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            font-size: 11px !important;
            margin-top: 8px !important;
        }

        .shift-break-title {
            color: #b45309 !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .shift-break-time {
            color: #92400e !important;
            font-weight: 800 !important;
            font-variant-numeric: tabular-nums;
        }

        .shift-arrow-btn {
            width: 32px !important;
            height: 32px !important;
            border-radius: 12px !important;
            background: rgba(var(--theme-rgb), 0.1) !important;
            color: var(--theme-primary) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 11px !important;
            transition: all 0.2s ease;
        }

        .shift-card:hover .shift-arrow-btn {
            background: var(--theme-primary) !important;
            color: #ffffff !important;
            transform: translateX(2px);
        }

        /* ===== Dark Mode Support ===== */
        html.dark body, body.dark {
            background-color: #070b14 !important;
            color: #f8fafc !important;
        }

        html.dark .shift-hero-banner, body.dark .shift-hero-banner {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 60%, #020617 100%) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 12px 30px -6px rgba(0, 0, 0, 0.65) !important;
        }

        html.dark .shift-card, body.dark .shift-card {
            background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 8px 25px -4px rgba(0, 0, 0, 0.45) !important;
        }

        html.dark .shift-card.active-shift-recommended, body.dark .shift-card.active-shift-recommended {
            border-color: rgba(56, 189, 248, 0.35) !important;
            box-shadow: 0 8px 25px -4px rgba(14, 165, 233, 0.15) !important;
        }

        html.dark .shift-card::before, body.dark .shift-card::before {
            background: linear-gradient(180deg, #38bdf8 0%, var(--theme-light) 100%);
        }

        html.dark .shift-recommended-badge, body.dark .shift-recommended-badge {
            background: rgba(56, 189, 248, 0.14);
            color: #38bdf8;
            border-color: rgba(56, 189, 248, 0.3);
        }

        html.dark .shift-title-text, body.dark .shift-title-text {
            color: #f8fafc !important;
        }

        html.dark .shift-timeline-box, body.dark .shift-timeline-box {
            background: #182339 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        html.dark .shift-time-label-text, body.dark .shift-time-label-text {
            color: #94a3b8 !important;
        }

        html.dark .shift-time-val-text, body.dark .shift-time-val-text {
            color: #f8fafc !important;
        }

        html.dark .shift-timeline-line, body.dark .shift-timeline-line {
            background: repeating-linear-gradient(
                to right,
                #334155 0,
                #334155 4px,
                transparent 4px,
                transparent 8px
            );
        }

        html.dark .shift-timeline-pill, body.dark .shift-timeline-pill {
            background: #0f172a;
            border-color: #334155;
            color: #38bdf8;
        }

        html.dark .shift-break-banner, body.dark .shift-break-banner {
            background: rgba(245, 158, 11, 0.12) !important;
            border-color: rgba(245, 158, 11, 0.28) !important;
        }

        html.dark .shift-break-title, body.dark .shift-break-title {
            color: #fbbf24 !important;
        }

        html.dark .shift-break-time, body.dark .shift-break-time {
            color: #fde68a !important;
        }

        html.dark .shift-arrow-btn, body.dark .shift-arrow-btn {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #cbd5e1 !important;
        }

        /* Staggered entrance animation */
        .fade-slide-up {
            animation: fadeSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(14px);
        }

        @keyframes fadeSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="px-1 pt-1 pb-24">
        
        {{-- ===== HERO BANNER SECTION ===== --}}
        <div class="shift-hero-banner mb-4 p-4 text-white">
            <div class="relative z-10">
                <div class="flex items-center justify-between gap-2 mb-2.5">
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-white inline-flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar-check text-[10px]"></i>
                        <span>{{ DateToIndo(date('Y-m-d')) }}</span>
                    </span>
                    <span class="text-[11.5px] font-extrabold tracking-wider px-2.5 py-1 rounded-full bg-black/20 backdrop-blur-md border border-white/15 text-white inline-flex items-center gap-1.5" style="font-variant-numeric: tabular-nums;">
                        <i class="fa-regular fa-clock text-[10px] opacity-80"></i>
                        <span id="liveClockDisplay">{{ date('H:i:s') }}</span>
                    </span>
                </div>
                
                <h2 class="text-[15.5px] font-extrabold tracking-tight mb-1 text-white">
                    Tentukan Jadwal Kerja Hari Ini
                </h2>
                <p class="text-[11.5px] text-white/85 leading-relaxed font-medium pr-6">
                    Pilih salah satu jam kerja yang sesuai dengan shift Anda sebelum melakukan foto presensi.
                </p>
            </div>
            
            {{-- Decorative Watermark & Glow --}}
            <div class="absolute -right-3 -bottom-4 text-white/10 text-6xl pointer-events-none">
                <i class="fa-solid fa-business-time"></i>
            </div>
            <div class="absolute -left-6 -top-6 w-24 h-24 rounded-full bg-white/10 pointer-events-none blur-lg"></div>
        </div>

        {{-- ===== SHIFT LIST SECTION HEADER ===== --}}
        <div class="flex items-center justify-between px-1.5 mb-2.5">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" style="background-color: var(--theme-primary);"></span>
                <span class="text-[11.5px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Daftar Shift Tersedia ({{ count($jamkerja) }})
                </span>
            </div>
            <span class="text-[10.5px] text-slate-500 dark:text-slate-400 font-semibold flex items-center gap-1">
                <i class="fa-regular fa-hand-pointer text-[10px]"></i>
                Ketuk untuk memilih
            </span>
        </div>

        <div class="space-y-2.5" id="shiftListContainer">
            @php
                $currentHour = date('H:i:s');
            @endphp

            @forelse ($jamkerja as $index => $item)
                @php
                    $delay = ($index + 1) * 0.06;
                    $jamMasuk = date('H:i', strtotime($item->jam_masuk));
                    $jamPulang = date('H:i', strtotime($item->jam_pulang));

                    // Check if current time is around shift time
                    $isRecommended = false;
                    $masukTime = strtotime($item->jam_masuk);
                    $pulangTime = strtotime($item->jam_pulang);
                    $nowTime = strtotime($currentHour);

                    if ($item->lintashari == 1) {
                        if ($nowTime >= $masukTime || $nowTime <= $pulangTime) {
                            $isRecommended = true;
                        }
                    } else {
                        if ($nowTime >= ($masukTime - 3600) && $nowTime <= ($pulangTime + 1800)) {
                            $isRecommended = true;
                        }
                    }

                    // Shift icon based on name
                    $iconClass = 'fa-sun text-amber-500';
                    $iconBg = 'bg-amber-50 dark:bg-amber-950/60 border-amber-200/80 dark:border-amber-800/60';
                    $nameLower = strtolower($item->nama_jam_kerja);
                    
                    if (str_contains($nameLower, 'malam') || $item->lintashari == 1) {
                        $iconClass = 'fa-moon text-indigo-500';
                        $iconBg = 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-200/80 dark:border-indigo-800/60';
                    } elseif (str_contains($nameLower, 'siang') || str_contains($nameLower, 'sore')) {
                        $iconClass = 'fa-cloud-sun text-orange-500';
                        $iconBg = 'bg-orange-50 dark:bg-orange-950/60 border-orange-200/80 dark:border-orange-800/60';
                    } elseif (str_contains($nameLower, 'pagi') || str_contains($nameLower, 'non')) {
                        $iconClass = 'fa-sun text-amber-500';
                        $iconBg = 'bg-amber-50 dark:bg-amber-950/60 border-amber-200/80 dark:border-amber-800/60';
                    }
                @endphp

                <div class="shift-card p-3.5 pl-4 fade-slide-up {{ $isRecommended ? 'active-shift-recommended' : '' }}"
                     style="animation-delay: {{ $delay }}s"
                     onclick="pilihJamKerja('{{ $item->kode_jam_kerja }}', '{{ $item->nama_jam_kerja }}')">
                    
                    {{-- Header Card --}}
                    <div class="flex items-center justify-between gap-2 pb-2.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-2xl {{ $iconBg }} flex items-center justify-center text-base shrink-0 border shadow-xs">
                                <i class="fa-solid {{ $iconClass }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="shift-title-text truncate">
                                    {{ $item->nama_jam_kerja }}
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                                        Kode: <strong class="text-slate-700 dark:text-slate-300">{{ $item->kode_jam_kerja }}</strong>
                                    </span>
                                    @if ($item->lintashari == 1)
                                        <span class="inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60">
                                            <i class="fa-solid fa-moon text-[8px]"></i>
                                            Lintas Hari
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Recommended badge & arrow --}}
                        <div class="shrink-0 flex items-center gap-1.5">
                            @if ($isRecommended)
                                <span class="shift-recommended-badge">
                                    <i class="fa-solid fa-wand-magic-sparkles text-[8.5px]"></i>
                                    <span>Disarankan</span>
                                </span>
                            @endif
                            <div class="shift-arrow-btn">
                                <i class="fa-solid fa-chevron-right"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Connected Timeline Window (Masuk -> Durasi -> Pulang) --}}
                    <div class="shift-timeline-box">
                        <div class="shift-time-endpoint">
                            <span class="shift-time-label-text">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                Jam Masuk
                            </span>
                            <span class="shift-time-val-text">
                                {{ $jamMasuk }}
                            </span>
                        </div>

                        <div class="shift-timeline-connector">
                            <div class="shift-timeline-line">
                                <span class="shift-timeline-pill">
                                    <i class="fa-regular fa-clock mr-0.5 text-[8.5px]"></i>
                                    {{ !empty($item->total_jam) ? $item->total_jam . ' Jam' : 'Shift' }}
                                </span>
                            </div>
                        </div>

                        <div class="shift-time-endpoint end-right">
                            <span class="shift-time-label-text">
                                Jam Pulang
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 inline-block"></span>
                            </span>
                            <span class="shift-time-val-text">
                                {{ $jamPulang }}
                            </span>
                        </div>
                    </div>

                    {{-- Istirahat Info (If applicable) --}}
                    @if ($item->istirahat == 1 && !empty($item->jam_awal_istirahat) && !empty($item->jam_akhir_istirahat))
                        <div class="shift-break-banner">
                            <span class="shift-break-title">
                                <i class="fa-solid fa-mug-hot text-[10px]"></i>
                                Waktu Istirahat
                            </span>
                            <span class="shift-break-time">
                                {{ date('H:i', strtotime($item->jam_awal_istirahat)) }} – {{ date('H:i', strtotime($item->jam_akhir_istirahat)) }}
                            </span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm">
                    <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Tidak Ada Jadwal Jam Kerja</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">
                        Belum ada jam kerja aktif yang dikonfigurasi pada sistem. Hubungi administrator/HRD Anda.
                    </p>
                    <a href="{{ route('dashboard.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white active:scale-95 transition-all"
                       style="background-color: var(--theme-primary);">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        // Live Clock
        function updateLiveClock() {
            var now = new Date();
            var h = String(now.getHours()).padStart(2, '0');
            var m = String(now.getMinutes()).padStart(2, '0');
            var s = String(now.getSeconds()).padStart(2, '0');
            var el = document.getElementById('liveClockDisplay');
            if (el) el.textContent = h + ':' + m + ':' + s;
            setTimeout(updateLiveClock, 1000);
        }
        updateLiveClock();

        // Handle Shift Selection
        function pilihJamKerja(kode_jam_kerja, nama_jam_kerja) {
            Swal.fire({
                title: 'Memilih ' + nama_jam_kerja,
                text: 'Membuka kamera & scanner presensi...',
                allowOutsideClick: false,
                showConfirmButton: false,
                timerProgressBar: true,
                timer: 900,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                window.location.href = '/presensi/create?kode_jam_kerja=' + encodeURIComponent(kode_jam_kerja);
            });
        }
    </script>
@endpush
