@extends('layouts.mobile.modern')

@section('title')
    <div class="text-center leading-tight">
        <div class="font-extrabold text-[15px] tracking-tight">Histori Presensi</div>
        <div class="text-[9.5px] font-medium opacity-75">SM-Attendance</div>
    </div>
@endsection

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@section('header_right')
    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white text-xs font-bold">
        <ion-icon name="calendar-outline" class="text-lg"></ion-icon>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        /* Bento Filter Card */
        .filter-card {
            background-color: #ffffff;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            transition: all 0.2s ease;
        }

        /* History Card */
        .histori-card {
            background-color: #ffffff;
            border-radius: 18px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .histori-card:active {
            transform: scale(0.98);
        }

        /* Dark Mode Overrides */
        html.dark body, body.dark {
            background-color: #070b14 !important;
            color: #f8fafc !important;
        }

        html.dark .filter-card, body.dark .filter-card {
            background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 8px 25px -4px rgba(0, 0, 0, 0.4) !important;
        }

        html.dark .histori-card, body.dark .histori-card {
            background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 8px 25px -4px rgba(0, 0, 0, 0.35) !important;
        }

        /* Air Datepicker Dark Mode */
        html.dark .air-datepicker, body.dark .air-datepicker {
            background: #0f172a !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: #f8fafc !important;
        }
        html.dark .air-datepicker-cell.-day-.-other-month-, body.dark .air-datepicker-cell.-day-.-other-month- {
            color: #475569 !important;
        }
        html.dark .air-datepicker-cell, body.dark .air-datepicker-cell {
            color: #cbd5e1 !important;
        }
        html.dark .air-datepicker-nav--title, body.dark .air-datepicker-nav--title {
            color: #f8fafc !important;
        }
        html.dark .air-datepicker-nav--action svg, body.dark .air-datepicker-nav--action svg {
            fill: #f8fafc !important;
        }
    </style>
@endpush

@section('content')
    <div class="px-1 pt-1 pb-24">

        {{-- ===== FILTER CARD ===== --}}
        <form method="GET" action="{{ route('presensi.histori') }}" id="formHistori">
            <div class="filter-card p-4 mb-4">
                <div class="flex items-center gap-2 pb-2.5 mb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs font-bold border border-teal-200/60 dark:border-teal-800/60">
                        <i class="fa-solid fa-filter"></i>
                    </div>
                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">
                        Filter Rentang Tanggal
                    </span>
                </div>

                <div class="grid grid-cols-5 gap-2 items-center">
                    {{-- Dari --}}
                    <div class="col-span-2">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Dari</label>
                        <input type="text" name="dari" id="dari" 
                            class="w-full rounded-xl py-2 px-2 text-xs font-bold text-center bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-teal-500/30 transition-all cursor-pointer"
                            placeholder="YYYY-MM-DD" value="{{ Request('dari') }}" autocomplete="off" required readonly>
                    </div>

                    {{-- Sampai --}}
                    <div class="col-span-2">
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Sampai</label>
                        <input type="text" name="sampai" id="sampai" 
                            class="w-full rounded-xl py-2 px-2 text-xs font-bold text-center bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-teal-500/30 transition-all cursor-pointer"
                            placeholder="YYYY-MM-DD" value="{{ Request('sampai') }}" autocomplete="off" required readonly>
                    </div>

                    {{-- Button Submit --}}
                    <div class="col-span-1 flex flex-col justify-end">
                        <label class="text-[10px] font-bold text-transparent block mb-1 select-none">Cari</label>
                        <button type="submit" id="btnCari"
                            class="w-full h-[37px] rounded-xl text-white flex items-center justify-center font-bold text-sm shadow-md active:scale-95 transition-all"
                            style="background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #0d9488 100%);">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- ===== SECTION TITLE & COUNT ===== --}}
        <div class="flex items-center justify-between px-2 mb-3">
            <span class="text-xs font-extrabold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                Catatan Presensi ({{ count($datapresensi) }})
            </span>
            @if(Request('dari') || Request('sampai'))
                <a href="{{ route('presensi.histori') }}" class="text-[11px] font-bold text-teal-600 dark:text-teal-400 hover:underline">
                    Reset Filter
                </a>
            @endif
        </div>

        {{-- ===== SKELETON LOADER ===== --}}
        <div id="skeleton-container" class="space-y-2.5">
            @for ($i = 0; $i < 4; $i++)
                <div class="histori-card p-3.5 flex items-center gap-3">
                    <div class="skeleton-avatar sk shrink-0 w-12 h-12 rounded-2xl"></div>
                    <div class="flex-1 space-y-2">
                        <div class="skeleton-text w-32 sk"></div>
                        <div class="skeleton-text w-48 sk"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- ===== DATA LIST CONTAINER ===== --}}
        <div id="data-container" style="display:none;" class="space-y-3">
            @forelse ($datapresensi as $index => $d)
                @php
                    $namahari = [
                        'Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu',
                        'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'
                    ];
                    $day_eng = date('D', strtotime($d->tanggal));
                    $day_indo = $namahari[$day_eng] ?? $day_eng;
                    $day_short = strtoupper(substr($day_indo, 0, 3));
                    $tgl = date('d', strtotime($d->tanggal));
                    $bulan_indo = getNamabulan((int)date('m', strtotime($d->tanggal)));
                    $tahun = date('Y', strtotime($d->tanggal));

                    $statusStyles = [
                        'h' => [
                            'label' => 'Hadir',
                            'badge' => 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                            'box' => 'bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-300/50 dark:border-emerald-700/50'
                        ],
                        'i' => [
                            'label' => 'Izin',
                            'badge' => 'bg-sky-100 dark:bg-sky-950/70 text-sky-800 dark:text-sky-300 border-sky-200 dark:border-sky-800/60',
                            'box' => 'bg-sky-500/10 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 border-sky-300/50 dark:border-sky-700/50'
                        ],
                        's' => [
                            'label' => 'Sakit',
                            'badge' => 'bg-rose-100 dark:bg-rose-950/70 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                            'box' => 'bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border-rose-300/50 dark:border-rose-700/50'
                        ],
                        'c' => [
                            'label' => 'Cuti',
                            'badge' => 'bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                            'box' => 'bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border-amber-300/50 dark:border-amber-700/50'
                        ],
                        'a' => [
                            'label' => 'Alpha',
                            'badge' => 'bg-red-100 dark:bg-red-950/70 text-red-800 dark:text-red-300 border-red-200 dark:border-red-800/60',
                            'box' => 'bg-red-500/10 dark:bg-red-500/20 text-red-600 dark:text-red-400 border-red-300/50 dark:border-red-700/50'
                        ],
                    ];
                    $st = $statusStyles[$d->status] ?? $statusStyles['a'];

                    $is_late = false;
                    $denda_display = 0;
                    $pulangcepat = 0;

                    if ($d->status == 'h') {
                        $jam_in_ts = strtotime($d->jam_in);
                        $jam_masuk_ts = strtotime($d->tanggal . ' ' . $d->jam_masuk);
                        $is_late = $jam_in_ts > $jam_masuk_ts;

                        if ($is_late && $d->jam_in) {
                            $terlambat_selisih = $jam_in_ts - $jam_masuk_ts;
                            $menit_telat = floor(($terlambat_selisih % 3600) / 60);
                            $denda_display = !empty($d->denda) ? $d->denda : hitungdenda($denda_list, $menit_telat);
                        }

                        $pulangcepat = hitungpulangcepat($d->tanggal, $d->jam_out, $d->jam_pulang, $d->istirahat, $d->jam_awal_istirahat, $d->jam_akhir_istirahat, $d->lintashari);
                    }
                @endphp

                <div class="histori-card p-3.5 fade-up" style="animation-delay: {{ $index * 0.03 }}s;">
                    {{-- Top Row: Date Box, Title & Status --}}
                    <div class="flex items-center gap-3">
                        {{-- Date Badge --}}
                        <div class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center border shrink-0 {{ $st['box'] }}">
                            <span class="text-[10px] font-extrabold uppercase leading-none">{{ $day_short }}</span>
                            <span class="text-base font-black leading-tight mt-0.5">{{ $tgl }}</span>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1.5 mb-1">
                                <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 truncate">
                                    {{ DateToIndo($d->tanggal) }}
                                </h3>
                                <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full border shrink-0 {{ $st['badge'] }}">
                                    {{ $st['label'] }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60">
                                    <i class="fa-solid fa-clock-rotate-left text-[9px] opacity-70"></i>
                                    {{ $d->nama_jam_kerja ?? 'Shift Reguler' }}
                                </span>

                                @if ($d->status == 'h')
                                    @if ($is_late)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg bg-rose-100 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60">
                                            <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                            Telat
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                            <i class="fa-solid fa-check text-[9px]"></i>
                                            Tepat Waktu
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Row: Time Stamps or Notes --}}
                    @if ($d->status == 'h')
                        <div class="grid grid-cols-2 gap-2 mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800">
                            <div class="bg-slate-50 dark:bg-slate-800/50 p-2 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Masuk:</span>
                                <span class="text-xs font-black text-slate-800 dark:text-slate-100 tracking-wider">
                                    {{ $d->jam_in ? date('H:i', strtotime($d->jam_in)) : '--:--' }}
                                </span>
                            </div>

                            <div class="bg-slate-50 dark:bg-slate-800/50 p-2 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Pulang:</span>
                                <span class="text-xs font-black text-slate-800 dark:text-slate-100 tracking-wider">
                                    {{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : '--:--' }}
                                </span>
                            </div>
                        </div>

                        {{-- Denda / Pulang Cepat Alerts --}}
                        @if ($denda_display > 0 || $pulangcepat > 0)
                            <div class="flex items-center gap-1.5 flex-wrap mt-2">
                                @if ($denda_display > 0)
                                    <span class="text-[9.5px] font-bold px-2 py-0.5 rounded-md bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800/60">
                                        Denda: Rp {{ number_format($denda_display, 0, ',', '.') }}
                                    </span>
                                @endif
                                @if ($pulangcepat > 0)
                                    <span class="text-[9.5px] font-bold px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                        Pulang Cepat
                                    </span>
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px]">
                            @if ($d->status == 'i')
                                <div class="text-sky-700 dark:text-sky-300 font-medium">
                                    <i class="fa-solid fa-info-circle mr-1"></i> Izin: {{ $d->keterangan_izin ?? '-' }}
                                </div>
                            @elseif ($d->status == 's')
                                <div class="text-rose-700 dark:text-rose-300 font-medium">
                                    <i class="fa-solid fa-notes-medical mr-1"></i> Sakit: {{ $d->keterangan_izin_sakit ?? '-' }}
                                </div>
                            @elseif ($d->status == 'c')
                                <div class="text-amber-700 dark:text-amber-300 font-medium">
                                    <i class="fa-solid fa-umbrella-beach mr-1"></i> Cuti: {{ $d->keterangan_izin_cuti ?? '-' }}
                                </div>
                            @else
                                <div class="text-red-700 dark:text-red-300 font-medium">
                                    <i class="fa-solid fa-circle-xmark mr-1"></i> Alpha: Tanpa Keterangan
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm">
                    <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Tidak Ada Data Histori</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">
                        Silakan pilih rentang tanggal lain untuk memuat histori absensi Anda.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script>
        const localeIndo = {
            days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
            months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            today: 'Hari ini', clear: 'Hapus', dateFormat: 'yyyy-MM-dd', timeFormat: 'HH:mm', firstDay: 1
        };
        const dpOpt = { locale: localeIndo, autoClose: true, isMobile: true, buttons: ['today', 'clear'], position: 'bottom center' };
        new AirDatepicker('#dari', dpOpt);
        new AirDatepicker('#sampai', dpOpt);

        function showSkeleton() { $('#data-container').hide(); $('#skeleton-container').show(); }
        function hideSkeleton() { $('#skeleton-container').fadeOut(150, function() { $('#data-container').fadeIn(200); }); }
        $('#formHistori').on('submit', function() { showSkeleton(); });
        $(document).ready(function() { setTimeout(hideSkeleton, 300); });
        $(window).on('load', function() { setTimeout(hideSkeleton, 150); });
    </script>
@endpush

