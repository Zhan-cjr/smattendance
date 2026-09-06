@extends('layouts.mobile.modern')
@section('title')
    <div class="text-center leading-tight">
        <div class="font-extrabold text-[15px] tracking-tight">SM-Attendance</div>
        <div class="text-[9.5px] font-medium opacity-75">by Zhansoft</div>
    </div>
@endsection

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@section('header_right')
    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white text-xs font-bold">
        <ion-icon name="time-outline" class="text-lg"></ion-icon>
    </div>
@endsection

@push('mystyle')
    <script>
        (function() {
            var savedTheme = localStorage.getItem('smatt_theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark', 'dark-mode-active');
                if (document.body) {
                    document.body.classList.remove('dark', 'dark-mode-active');
                }
            }
        })();
    </script>
    <!-- Google Fonts: Plus Jakarta Sans & Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        /* ===== Shift Card Bento Styling ===== */
        .shift-card {
            background-color: #ffffff !important;
            border-radius: 20px !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 4px 18px -2px rgba(15, 23, 42, 0.06) !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .shift-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.12) !important;
            border-color: {{ $t['primary'] ?? '#0f766e' }} !important;
        }

        .shift-card:active {
            transform: scale(0.98);
        }

        .shift-card.active-shift-recommended {
            border-color: #10b981 !important;
            box-shadow: 0 8px 24px -4px rgba(16, 185, 129, 0.22) !important;
        }

        .shift-title-text {
            color: #0f172a !important;
            font-weight: 800 !important;
            font-size: 14.5px !important;
            line-height: 1.25 !important;
        }

        .shift-time-card {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            padding: 10px 8px !important;
            text-align: center !important;
        }

        .shift-time-label-text {
            font-size: 10px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            display: block !important;
        }

        .shift-time-val-text {
            font-size: 16.5px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            letter-spacing: 0.03em !important;
            display: block !important;
            margin-top: 2px !important;
        }

        .shift-break-banner {
            background-color: #fef3c7 !important;
            border: 1px solid #fde68a !important;
            border-radius: 12px !important;
            padding: 6px 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            font-size: 11px !important;
            margin-top: 10px !important;
        }

        .shift-break-title {
            color: #92400e !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .shift-break-time {
            color: #78350f !important;
            font-weight: 800 !important;
        }

        .shift-arrow-btn {
            width: 32px !important;
            height: 32px !important;
            border-radius: 9999px !important;
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 11px !important;
        }

        /* ===== Dark Mode Support ===== */
        html.dark body, body.dark {
            background-color: #070b14 !important;
            color: #f8fafc !important;
        }

        html.dark .shift-card, body.dark .shift-card {
            background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 8px 25px -4px rgba(0, 0, 0, 0.4) !important;
        }

        html.dark .shift-title-text, body.dark .shift-title-text {
            color: #f8fafc !important;
        }

        html.dark .shift-time-card, body.dark .shift-time-card {
            background-color: #182339 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        html.dark .shift-time-label-text, body.dark .shift-time-label-text {
            color: #94a3b8 !important;
        }

        html.dark .shift-time-val-text, body.dark .shift-time-val-text {
            color: #f8fafc !important;
        }

        html.dark .shift-break-banner, body.dark .shift-break-banner {
            background-color: rgba(217, 119, 6, 0.15) !important;
            border-color: rgba(217, 119, 6, 0.3) !important;
        }

        html.dark .shift-break-title, body.dark .shift-break-title {
            color: #fbbf24 !important;
        }

        html.dark .shift-break-time, body.dark .shift-break-time {
            color: #fef3c7 !important;
        }

        html.dark .shift-arrow-btn, body.dark .shift-arrow-btn {
            background-color: #1e293b !important;
            color: #94a3b8 !important;
        }

        /* Staggered entrance animation */
        .fade-slide-up {
            animation: fadeSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(16px);
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
        <div class="mb-4 p-4 rounded-3xl text-white relative overflow-hidden shadow-md"
             style="background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #115e59 50%, #042f2e 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-white/20 backdrop-blur-md border border-white/20 text-white flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-day text-[10px] text-emerald-300"></i>
                        <span>{{ DateToIndo(date('Y-m-d')) }}</span>
                    </span>
                    <span class="text-xs font-black tracking-wider text-emerald-200" id="liveClockDisplay">
                        {{ date('H:i:s') }}
                    </span>
                </div>
                
                <h2 class="text-base font-extrabold tracking-tight mb-1 text-white">
                    Tentukan Jadwal Kerja Hari Ini
                </h2>
                <p class="text-xs text-emerald-100/90 leading-relaxed font-medium">
                    Pilih salah satu jam kerja yang sesuai dengan shift Anda sebelum melakukan foto presensi.
                </p>
            </div>
            
            {{-- Decorative circles --}}
            <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-white/10 pointer-events-none blur-xl"></div>
            <div class="absolute -left-6 -top-6 w-24 h-24 rounded-full bg-emerald-400/20 pointer-events-none blur-lg"></div>
        </div>

        {{-- ===== SHIFT LIST SECTION ===== --}}
        <div class="flex items-center justify-between px-2 mb-3">
            <span class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                Daftar Shift Tersedia ({{ count($jamkerja) }})
            </span>
            <span class="text-[11px] text-slate-500 font-medium">Ketuk untuk memilih</span>
        </div>

        <div class="space-y-3" id="shiftListContainer">
            @php
                $currentHour = date('H:i:s');
            @endphp

            @forelse ($jamkerja as $index => $item)
                @php
                    $delay = ($index + 1) * 0.08;
                    $jamMasuk = date('H:i', strtotime($item->jam_masuk));
                    $jamPulang = date('H:i', strtotime($item->jam_pulang));

                    // Check if current time is somewhat around shift time
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
                    $iconClass = 'fa-sun text-amber-600';
                    $iconBg = 'bg-amber-100 dark:bg-amber-950/60 border-amber-300 dark:border-amber-800/60';
                    $nameLower = strtolower($item->nama_jam_kerja);
                    
                    if (str_contains($nameLower, 'malam') || $item->lintashari == 1) {
                        $iconClass = 'fa-moon text-indigo-500';
                        $iconBg = 'bg-indigo-100 dark:bg-indigo-950/60 border-indigo-300 dark:border-indigo-800/60';
                    } elseif (str_contains($nameLower, 'siang') || str_contains($nameLower, 'sore')) {
                        $iconClass = 'fa-cloud-sun text-orange-600';
                        $iconBg = 'bg-orange-100 dark:bg-orange-950/60 border-orange-300 dark:border-orange-800/60';
                    } elseif (str_contains($nameLower, 'pagi') || str_contains($nameLower, 'non')) {
                        $iconClass = 'fa-sun text-amber-600';
                        $iconBg = 'bg-amber-100 dark:bg-amber-950/60 border-amber-300 dark:border-amber-800/60';
                    }
                @endphp

                <div class="shift-card p-4 fade-slide-up {{ $isRecommended ? 'active-shift-recommended' : '' }}"
                     style="animation-delay: {{ $delay }}s"
                     onclick="pilihJamKerja('{{ $item->kode_jam_kerja }}', '{{ $item->nama_jam_kerja }}')">
                    
                    {{-- Header Card --}}
                    <div class="flex items-start justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-2xl {{ $iconBg }} flex items-center justify-center text-base shrink-0 border transition-all">
                                <i class="fa-solid {{ $iconClass }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="shift-title-text truncate">
                                    {{ $item->nama_jam_kerja }}
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                    @if ($item->lintashari == 1)
                                        <span class="inline-flex items-center gap-1 text-[9.5px] font-bold px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border border-purple-300 dark:border-purple-800/60">
                                            <i class="fa-solid fa-moon text-[8.5px]"></i>
                                            Lintas Hari
                                        </span>
                                    @endif

                                    @if (!empty($item->total_jam))
                                        <span class="inline-flex items-center gap-1 text-[9.5px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            <i class="fa-solid fa-hourglass-half text-[8.5px]"></i>
                                            {{ $item->total_jam }} Jam
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Recommended badge or arrow --}}
                        <div class="shrink-0 flex items-center gap-1.5">
                            @if ($isRecommended)
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700/80 flex items-center gap-1 animate-pulse">
                                    <i class="fa-solid fa-star text-[8.5px]"></i>
                                    <span>Disarankan</span>
                                </span>
                            @endif
                            <div class="shift-arrow-btn">
                                <i class="fa-solid fa-chevron-right"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Time Window Details (Masuk & Pulang) --}}
                    <div class="grid grid-cols-2 gap-2.5 mt-3">
                        <div class="shift-time-card">
                            <span class="shift-time-label-text">Jam Masuk</span>
                            <span class="shift-time-val-text">
                                {{ $jamMasuk }}
                            </span>
                        </div>

                        <div class="shift-time-card">
                            <span class="shift-time-label-text">Jam Pulang</span>
                            <span class="shift-time-val-text">
                                {{ $jamPulang }}
                            </span>
                        </div>
                    </div>

                    {{-- Istirahat Info (If applicable) --}}
                    @if ($item->istirahat == 1 && !empty($item->jam_awal_istirahat) && !empty($item->jam_akhir_istirahat))
                        <div class="shift-break-banner">
                            <span class="shift-break-title">
                                <i class="fa-solid fa-mug-hot text-[10px] text-amber-700 dark:text-amber-400"></i>
                                Istirahat Kerja:
                            </span>
                            <span class="shift-break-time">
                                {{ date('H:i', strtotime($item->jam_awal_istirahat)) }} - {{ date('H:i', strtotime($item->jam_akhir_istirahat)) }}
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
                    <a href="{{ route('dashboard.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-white dark:bg-slate-700 active:scale-95 transition-all">
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
        // Synchronize dark theme with localStorage
        (function() {
            var savedTheme = localStorage.getItem('smatt_theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark', 'dark-mode-active');
                if (document.body) {
                    document.body.classList.remove('dark', 'dark-mode-active');
                }
            }
        })();

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
