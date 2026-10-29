@extends('v2.layouts.app')

@section('titlepage', 'Monitoring Presensi')
@section('page_title', 'Monitoring Presensi')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $selectedDate = !empty(request('tanggal')) ? request('tanggal') : date('Y-m-d');
    
    // Attendance Statistics across ENTIRE dataset (not per-page pagination)
    $totalKaryawan = $stats['total'] ?? $karyawan->total();
    $countHadir = $stats['hadir'] ?? $karyawan->where('status', 'h')->count();
    $countIzin = $stats['izin'] ?? $karyawan->where('status', 'i')->count();
    $countSakit = $stats['sakit'] ?? $karyawan->where('status', 's')->count();
    $countCuti = $stats['cuti'] ?? $karyawan->where('status', 'c')->count();
    $countAlpa = $stats['alpa'] ?? $karyawan->where('status', 'a')->count();
    $countBelumAbsen = $stats['belum_absen'] ?? $karyawan->whereNull('status')->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMBS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-device-desktop-analytics text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Monitoring Presensi Harian</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pantau kehadiran real-time, foto selfie, plotting GPS radius, log mesin fingerprint & koreksi absensi
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Quick Date Info -->
        <div class="flex flex-col md:items-end gap-2.5">
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-activity text-sm"></i>
                    <span>Aktivitas</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Monitoring Presensi</span>
            </nav>

            <!-- Active Date Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs">
                <i class="ti ti-calendar text-emerald-600"></i>
                <span>Tanggal: <strong class="text-slate-900">{{ DateToIndo($selectedDate) }}</strong></span>
            </div>
        </div>
    </div>

    <!-- ================= 2. EXECUTIVE METRICS CARD (SEAMLESS SOLID/GRADIENT CARD WITH TAPERED DIVIDERS - CONSISTENT V2) ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-fingerprint text-[200px]"></i>
        </div>

        <div class="relative z-10 flex flex-wrap lg:flex-nowrap items-stretch justify-between gap-y-6">
            
            <!-- Segment 1: Hadir -->
            <div class="flex-1 min-w-[120px] sm:min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Hadir
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Masuk
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($countHadir) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-circle-check text-xs opacity-80"></i>
                        <span>Tercatat Hadir</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/20 px-2 py-0.5 rounded-md">
                        {{ $totalKaryawan > 0 ? round(($countHadir / $totalKaryawan) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden lg:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Izin -->
            <div class="flex-1 min-w-[120px] sm:min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Izin
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/15 text-emerald-100 border border-white/20">
                            Izin
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($countIzin) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-file-description text-xs opacity-80"></i>
                        <span>Disetujui</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $totalKaryawan > 0 ? round(($countIzin / $totalKaryawan) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden lg:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Sakit -->
            <div class="flex-1 min-w-[120px] sm:min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Sakit
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/15 text-emerald-100 border border-white/20">
                            Sakit
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($countSakit) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-ambulance text-xs opacity-80"></i>
                        <span>Surat Dokter</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $totalKaryawan > 0 ? round(($countSakit / $totalKaryawan) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden lg:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 4: Cuti -->
            <div class="flex-1 min-w-[120px] sm:min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Cuti
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/15 text-emerald-100 border border-white/20">
                            Cuti
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($countCuti) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-calendar-event text-xs opacity-80"></i>
                        <span>Cuti Tahunan</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $totalKaryawan > 0 ? round(($countCuti / $totalKaryawan) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden lg:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 5: Alpha -->
            <div class="flex-1 min-w-[120px] sm:min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Alpha
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/15 text-rose-200 border border-white/20">
                            Alpha
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($countAlpa) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-x text-xs opacity-80"></i>
                        <span>Tanpa Keterangan</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $totalKaryawan > 0 ? round(($countAlpa / $totalKaryawan) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden lg:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 6: Belum Absen -->
            <div class="flex-1 min-w-[120px] sm:min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Belum Absen
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/15 text-slate-200 border border-white/20">
                            Pending
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($countBelumAbsen) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-clock-off text-xs opacity-80"></i>
                        <span>Menunggu</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $totalKaryawan > 0 ? round(($countBelumAbsen / $totalKaryawan) * 100) : 0 }}%
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= 3. FILTER TOOLBAR (KONSISTEN DENGAN HALAMAN KARYAWAN) ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('presensi.index') }}" method="GET" id="filterPresensiForm" class="w-full">
            <div class="flex flex-col lg:flex-row items-center gap-2.5 sm:gap-3 w-full">
                
                <!-- Search Input (Expanding, pl-11 clean separation) -->
                <div class="flex-1 min-w-0 w-full relative">
                    <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none"></i>
                    <input type="text" 
                           name="nama_karyawan" 
                           id="filter_nama"
                           value="{{ request('nama_karyawan') }}" 
                           placeholder="Cari Nama Karyawan / NIK..." 
                           class="w-full pl-11 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <!-- Tanggal Filter -->
                <div class="w-full sm:w-auto lg:w-52 shrink-0 relative">
                    <i class="ti ti-calendar absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none z-10"></i>
                    <input type="text" 
                           name="tanggal" 
                           id="filter_tanggal" 
                           value="{{ request('tanggal', date('Y-m-d')) }}" 
                           placeholder="Pilih Tanggal"
                           class="w-full pl-11 pr-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-semibold shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                </div>

                <!-- Cabang Filter -->
                <div class="w-full sm:w-auto lg:w-48 shrink-0">
                    <select name="kode_cabang" id="filter_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons: Cari / Filter & Reset -->
                <div class="flex items-center gap-1.5 shrink-0 w-full lg:w-auto">
                    <button type="submit" 
                            class="w-full lg:w-auto px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['nama_karyawan', 'kode_cabang', 'tanggal']))
                        <a href="{{ route('presensi.index') }}" 
                           class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    <!-- ================= 4. MAIN DISPLAY (CARDS VIEW) ================= -->
    <div class="space-y-2">
        @forelse ($karyawan as $d)
            @php
                $tanggal_presensi = !empty(request('tanggal')) ? request('tanggal') : date('Y-m-d');
                $jam_masuk = $tanggal_presensi . ' ' . $d->jam_masuk;
                $terlambat = hitungjamterlambat($d->jam_in, $jam_masuk);
                $potongan_tidak_hadir = $d->status == 'a' ? $d->total_jam : 0;
                $pulangcepat = hitungpulangcepat(
                    $tanggal_presensi,
                    $d->jam_out,
                    $d->jam_pulang,
                    $d->istirahat,
                    $d->jam_awal_istirahat,
                    $d->jam_akhir_istirahat,
                    $d->lintashari,
                );

                if ($d->denda !== null) {
                    $denda = $d->denda;
                    if ($terlambat != null) {
                        $potongan_jam_terlambat = $terlambat['desimal_terlambat'] >= 1 ? $terlambat['desimal_terlambat'] : 0;
                    } else {
                        $potongan_jam_terlambat = 0;
                    }
                } else {
                    if ($terlambat != null) {
                        if ($terlambat['desimal_terlambat'] < 1) {
                            $potongan_jam_terlambat = 0;
                            $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                        } else {
                            $potongan_jam_terlambat = $terlambat['desimal_terlambat'];
                            $denda = 0;
                        }
                    } else {
                        $potongan_jam_terlambat = 0;
                        $denda = 0;
                    }
                }
                
                $total_potongan_jam = $pulangcepat + $potongan_jam_terlambat + $potongan_tidak_hadir;
            @endphp

            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs hover:shadow-sm hover:border-slate-300 transition-all duration-200 overflow-hidden group">
                <div class="p-2.5 sm:p-3">
                    
                    <!-- Row 1: Employee Header & Status Actions (Ultra Compact & Clean) -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-slate-100">
                        
                        <!-- Left: Info Identitas Karyawan (Dengan Foto Profil Compact) -->
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="relative shrink-0">
                                @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                    <img src="{{ getfotoKaryawan($d->foto) }}" alt="{{ $d->nama_karyawan }}" class="w-8 h-9 rounded-lg object-cover border border-slate-200 shadow-2xs">
                                @else
                                    <div class="w-8 h-9 rounded-lg bg-orange-500 text-white border border-orange-600/30 flex items-center justify-center font-black text-xs shadow-2xs">
                                        {{ strtoupper(substr($d->nama_karyawan, 0, 1)) }}
                                    </div>
                                @endif

                                @if($d->status == 'h')
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[8px] ring-1.5 ring-white shadow-2xs" title="Hadir">
                                        <i class="ti ti-check"></i>
                                    </span>
                                @elseif($d->status == 'a')
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-rose-500 text-white flex items-center justify-center text-[8px] ring-1.5 ring-white shadow-2xs" title="Alpa">
                                        <i class="ti ti-x"></i>
                                    </span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h6 class="font-bold text-slate-900 text-xs sm:text-[13px] mb-0 group-hover:text-emerald-700 transition truncate max-w-[200px] sm:max-w-xs md:max-w-md">
                                        {{ $d->nama_karyawan }}
                                    </h6>
                                    <span class="font-mono text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                        {{ $d->nik_show ?? $d->nik }}
                                    </span>
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200/70">
                                        <i class="ti ti-building text-[10px] text-slate-400"></i> {{ $d->kode_dept }}
                                    </span>
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200/70">
                                        <i class="ti ti-map-pin text-[10px] text-slate-400"></i> {{ $d->kode_cabang }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Status Badge & Action Buttons -->
                        <div class="flex items-center gap-1.5 justify-between sm:justify-end shrink-0">
                            
                            <!-- Status Badge -->
                            <div>
                                @if ($d->status == 'h')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-black tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ti ti-circle-check text-[11px]"></i> Hadir
                                    </span>
                                @elseif($d->status == 'i')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-black tracking-wide bg-sky-50 text-sky-700 border border-sky-200">
                                        <i class="ti ti-file-info text-[11px]"></i> Izin
                                    </span>
                                @elseif($d->status == 's')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-black tracking-wide bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="ti ti-ambulance text-[11px]"></i> Sakit
                                    </span>
                                @elseif($d->status == 'a')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-black tracking-wide bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="ti ti-x text-[11px]"></i> Alpa
                                    </span>
                                @elseif($d->status == 'c')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-black tracking-wide bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i class="ti ti-calendar-event text-[11px]"></i> Cuti
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-black tracking-wide bg-slate-100 text-slate-500 border border-slate-200">
                                        <i class="ti ti-minus text-[11px]"></i> Belum Absen
                                    </span>
                                @endif
                            </div>

                            <!-- Grouped Action Buttons -->
                            <div class="inline-flex items-center border border-slate-200 rounded-lg p-0.5 bg-slate-50">
                                @if (isset($d->status_potongan))
                                    <span class="w-6 h-6 rounded flex items-center justify-center text-slate-400 bg-slate-100" title="Data presensi terkunci dalam laporan">
                                        <i class="ti ti-lock text-xs"></i>
                                    </span>
                                @else
                                    <!-- Koreksi Presensi -->
                                    <button type="button" 
                                            class="w-6 h-6 rounded hover:bg-white text-blue-600 hover:text-blue-700 flex items-center justify-center transition cursor-pointer koreksiPresensi"
                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                            tanggal="{{ $tanggal_presensi }}" 
                                            title="Koreksi Presensi">
                                        <i class="ti ti-edit text-xs"></i>
                                    </button>

                                    <!-- Hapus Presensi -->
                                    @if(!empty($d->id))
                                        <form action="{{ route('presensi.delete', $d->id) }}" method="POST" class="m-0 deleteform">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-6 h-6 rounded hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition cursor-pointer delete-confirm"
                                                    title="Hapus Presensi Hari Ini">
                                                <i class="ti ti-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                <!-- Get Log Mesin -->
                                <button type="button" 
                                        class="w-6 h-6 rounded hover:bg-white text-emerald-600 hover:text-emerald-700 flex items-center justify-center transition cursor-pointer btngetDatamesin"
                                        pin="{{ $d->pin }}"
                                        tanggal="{{ $tanggal_presensi }}" 
                                        title="Tarik Data Log Mesin">
                                    <i class="ti ti-device-desktop text-xs"></i>
                                </button>
                            </div>

                        </div>
                    </div>

                    <!-- Row 2: Attendance Metrics (Dotted Divider, Clean & Seamless) -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-y-2.5 gap-x-3 pt-2 items-center divide-y sm:divide-y-0 sm:divide-x divide-dashed divide-slate-200">
                        
                        <!-- 1. Jadwal Shift -->
                        <div class="flex items-center gap-2 px-1">
                            <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center text-[11px] shrink-0">
                                <i class="ti ti-clock"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider leading-none">Jadwal</span>
                                @if ($d->kode_jam_kerja != null)
                                    <div class="mt-0.5 leading-none truncate">
                                        <span class="font-bold text-slate-800 text-[11px]" title="{{ $d->nama_jam_kerja }}">{{ $d->nama_jam_kerja }}</span>
                                        <span class="text-[9.5px] text-slate-500 font-mono ml-0.5">({{ date('H:i', strtotime($d->jam_masuk)) }}-{{ date('H:i', strtotime($d->jam_pulang)) }})</span>
                                    </div>
                                @else
                                    <span class="text-[11px] font-bold text-slate-400 block mt-0.5 leading-none">-</span>
                                @endif
                            </div>
                        </div>

                        <!-- 2. Jam Masuk -->
                        <div class="flex items-center gap-2 px-1 sm:pl-3 pt-2 sm:pt-0">
                            <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-[11px] shrink-0">
                                <i class="ti ti-login"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider leading-none">Masuk</span>
                                @if ($d->jam_in != null)
                                    <div class="flex items-center gap-1 flex-wrap mt-0.5 leading-none">
                                        <button type="button" 
                                                class="btnShowpresensi font-bold text-slate-900 hover:text-emerald-700 text-[11.5px] underline decoration-dotted transition cursor-pointer" 
                                                data-id="{{ $d->id }}" 
                                                data-status="in"
                                                title="Lihat Foto & Lokasi Masuk">
                                            {{ date('H:i', strtotime($d->jam_in)) }}
                                        </button>
                                        @if (!empty($d->foto_in))
                                            <i class="ti ti-photo text-emerald-600 text-[11px]" title="Ada Foto Selfie"></i>
                                        @endif
                                        @if ($potongan_jam_terlambat > 0)
                                            <span class="text-[8.5px] font-bold text-rose-600 bg-rose-50 px-1 py-0.2 rounded border border-rose-100">
                                                -{{ $potongan_jam_terlambat }}j
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] font-bold text-slate-400 block mt-0.5 leading-none">-</span>
                                @endif
                            </div>
                        </div>

                        <!-- 3. Jam Pulang -->
                        <div class="flex items-center gap-2 px-1 sm:pl-3 pt-2 sm:pt-0">
                            <div class="w-6 h-6 rounded-md bg-rose-50 text-rose-600 flex items-center justify-center text-[11px] shrink-0">
                                <i class="ti ti-logout"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider leading-none">Pulang</span>
                                @if ($d->jam_out != null)
                                    <div class="flex items-center gap-1 flex-wrap mt-0.5 leading-none">
                                        <button type="button" 
                                                class="btnShowpresensi font-bold text-slate-900 hover:text-rose-700 text-[11.5px] underline decoration-dotted transition cursor-pointer" 
                                                data-id="{{ $d->id }}" 
                                                data-status="out"
                                                title="Lihat Foto & Lokasi Pulang">
                                            {{ date('H:i', strtotime($d->jam_out)) }}
                                        </button>
                                        @if (!empty($d->foto_out))
                                            <i class="ti ti-photo text-rose-600 text-[11px]" title="Ada Foto Selfie"></i>
                                        @endif
                                        @if ($pulangcepat > 0)
                                            <span class="text-[8.5px] font-bold text-rose-600 bg-rose-50 px-1 py-0.2 rounded border border-rose-100">
                                                -{{ $pulangcepat }}j
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] font-bold text-slate-400 block mt-0.5 leading-none">-</span>
                                @endif
                            </div>
                        </div>

                        <!-- 4. Terlambat -->
                        <div class="flex items-center gap-2 px-1 sm:pl-3 pt-2 sm:pt-0">
                            <div class="w-6 h-6 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center text-[11px] shrink-0">
                                <i class="ti ti-clock-exclamation"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider leading-none">Terlambat</span>
                                @if($terlambat != null)
                                    <span class="font-bold text-rose-600 text-[11px] block mt-0.5 leading-none truncate">
                                        {!! $terlambat['show'] !!}
                                    </span>
                                @else
                                    <span class="font-bold text-emerald-700 text-[11px] flex items-center gap-0.5 mt-0.5 leading-none">
                                        <i class="ti ti-check text-[10px]"></i> Tepat Waktu
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- 5. Denda -->
                        <div class="flex items-center gap-2 px-1 sm:pl-3 pt-2 sm:pt-0">
                            <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center text-[11px] shrink-0">
                                <i class="ti ti-coin"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider leading-none">Denda</span>
                                <span class="font-bold text-[11px] {{ empty($denda) ? 'text-slate-400' : 'text-rose-600' }} block mt-0.5 leading-none">
                                    Rp {{ empty($denda) ? '0' : formatAngka($denda) }}
                                </span>
                            </div>
                        </div>

                        <!-- 6. Potongan -->
                        <div class="flex items-center gap-2 px-1 sm:pl-3 pt-2 sm:pt-0">
                            <div class="w-6 h-6 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center text-[11px] shrink-0">
                                <i class="ti ti-cut"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider leading-none">Potongan</span>
                                @if ($total_potongan_jam > 0)
                                    <span class="font-bold text-rose-600 text-[11px] block mt-0.5 leading-none">
                                        {{ formatAngkaDesimal($total_potongan_jam) }} Jam
                                    </span>
                                @else
                                    <span class="font-bold text-emerald-700 text-[11px] block mt-0.5 leading-none">
                                        0 Jam
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/90 p-12 text-center text-slate-400 shadow-2xs">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-3xl text-slate-400 mx-auto mb-3">
                    <i class="ti ti-user-x"></i>
                </div>
                <h6 class="text-sm font-bold text-slate-700 mb-1">Tidak Ada Data Presensi Karyawan</h6>
                <p class="text-xs text-slate-400 mb-0">Coba ubah tanggal atau filter cabang untuk melihat rekaman presensi lainnya.</p>
            </div>
        @endforelse
    </div>

    <!-- ================= 5. PAGINATION ================= -->
    <div class="pt-2">
        {{ $karyawan->links('v2.components.pagination') }}
    </div>

</div>

<!-- ================= MODAL CONTAINER ================= -->
<div class="modal fade" id="modalPresensiV2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-device-desktop-analytics" id="modalPresensiIcon"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalPresensiTitle">Detail Presensi</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalPresensiSubtitle">Informasi lengkap presensi, foto selfie dan koordinat GPS</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer shrink-0" data-bs-dismiss="modal">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6" id="loadModalPresensiContent">
                <!-- AJAX Content Loaded Here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        const modalEl = document.getElementById('modalPresensiV2');
        const modalInstance = new bootstrap.Modal(modalEl);

        // Datepicker init on filter
        function initFlatpickr() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr("#filter_tanggal", {
                    locale: "id",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d F Y",
                    allowInput: false,
                    static: false
                });
            }
        }
        initFlatpickr();

        function showLoading() {
            $("#loadModalPresensiContent").html(`
                <div class="py-12 text-center text-slate-400">
                    <div style="width: 36px; height: 36px; border: 3px solid #059669; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 14px;"></div>
                    <p class="text-xs font-bold text-slate-700 mb-0">Memuat Data Presensi...</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Mengambil lampiran foto, koordinat & status log</p>
                </div>
            `);
        }

        // 1. Show Presensi Detail (Foto & GPS Map)
        $(document).on('click', '.btnShowpresensi', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const status = $(this).data('status');

            $('#modalPresensiTitle').text(status === 'in' ? 'Detail Presensi Masuk' : 'Detail Presensi Pulang');
            $('#modalPresensiSubtitle').text('Informasi visual foto selfie dan verifikasi radius GPS kantor');
            $('#modalPresensiIcon').attr('class', 'ti ti-camera');

            showLoading();
            modalInstance.show();
            $('#loadModalPresensiContent').load(`/presensi/${id}/${status}/show`);
        });

        // 2. Koreksi Presensi Trigger
        $(document).on('click', '.koreksiPresensi', function(e) {
            e.preventDefault();
            const nik = $(this).attr('nik');
            const tanggal = $(this).attr('tanggal');

            $('#modalPresensiTitle').text('Koreksi Data Presensi');
            $('#modalPresensiSubtitle').text('Perbarui status kehadiran, shift kerja, dan jam masuk/pulang');
            $('#modalPresensiIcon').attr('class', 'ti ti-edit');

            showLoading();
            modalInstance.show();
            $.ajax({
                type: 'POST',
                url: "{{ route('presensi.edit') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    nik: nik,
                    tanggal: tanggal
                },
                success: function(res) {
                    $('#loadModalPresensiContent').html(res);
                },
                error: function() {
                    $('#loadModalPresensiContent').html(`
                        <div class="p-6 text-center text-rose-600">
                            <i class="ti ti-alert-circle text-2xl mb-1 block"></i>
                            <span class="text-xs font-bold">Gagal memuat formulir koreksi presensi.</span>
                        </div>
                    `);
                }
            });
        });

        // 3. Get Data Mesin Trigger
        $(document).on('click', '.btngetDatamesin', function(e) {
            e.preventDefault();
            const pin = $(this).attr('pin');
            const tanggal = $(this).attr('tanggal');

            $('#modalPresensiTitle').text('Tarik Log Mesin Fingerprint');
            $('#modalPresensiSubtitle').text('Daftar riwayat scan mesin ADMS Cloud & sinkronisasi lokal');
            $('#modalPresensiIcon').attr('class', 'ti ti-device-desktop');

            showLoading();
            modalInstance.show();
            $.ajax({
                type: 'POST',
                url: "{{ route('presensi.getdatamesin') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    pin: pin,
                    tanggal: tanggal
                },
                success: function(res) {
                    $('#loadModalPresensiContent').html(res);
                },
                error: function() {
                    $('#loadModalPresensiContent').html(`
                        <div class="p-6 text-center text-rose-600">
                            <i class="ti ti-alert-circle text-2xl mb-1 block"></i>
                            <span class="text-xs font-bold">Gagal mengambil log dari server mesin.</span>
                        </div>
                    `);
                }
            });
        });

        // 4. Delete Presensi Confirmation
        $(document).on('click', '.delete-confirm', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Presensi Hari Ini?',
                    text: "Data rekaman absensi, foto selfie, dan denda akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Hapus Presensi!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-slate-100',
                        confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold shadow-xs',
                        cancelButton: 'px-4 py-2 rounded-xl text-xs font-bold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Apakah Anda yakin ingin menghapus data presensi ini?')) {
                    form.submit();
                }
            }
        });
    });
</script>
@endpush
