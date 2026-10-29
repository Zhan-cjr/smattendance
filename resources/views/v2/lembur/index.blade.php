@extends('v2.layouts.app')

@section('titlepage', 'Data Lembur')
@section('page_title', 'Pengajuan & Jadwal Lembur')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalLembur = $stats['total'] ?? $lembur->total();
    $totalPending = $stats['pending'] ?? 0;
    $totalApproved = $stats['approved'] ?? 0;
    $totalRejected = $stats['rejected'] ?? 0;
    $persenPending = $totalLembur > 0 ? round(($totalPending / $totalLembur) * 100) : 0;
    $persenApproved = $totalLembur > 0 ? round(($totalApproved / $totalLembur) * 100) : 0;
    $persenRejected = $totalLembur > 0 ? round(($totalRejected / $totalLembur) * 100) : 0;
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-clock-play text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Lembur Karyawan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola surat perintah lembur (SPL), persetujuan approval, serta realisasi absensi lembur
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-calendar text-sm"></i>
                    <span>Aktivitas</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Lembur</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('lembur.create')
                    <a href="{{ (\Illuminate\Support\Facades\Route::has('lembur.template') ? route('lembur.template') : '#') }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 text-slate-700 hover:bg-slate-50 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                        <i class="ti ti-template text-sm text-emerald-600"></i>
                        <span>Template SPL</span>
                    </a>
                    
                    <button type="button" id="btnCreateLembur" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Buat Surat Lembur</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. EXECUTIVE STATISTICS BANNER (CONSISTENT WITH KARYAWAN & GRUP V2) ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-clock-play text-[190px]"></i>
        </div>

        <!-- Period & Year Selector Dropdown on the Card -->
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-3 pb-3 mb-4 border-b border-white/15">
            <div class="flex items-center gap-2">
                <i class="ti ti-chart-bar text-white/80 text-base"></i>
                <span class="text-xs sm:text-sm font-bold text-white tracking-wide">Statistik Lembur</span>
                @if(!empty(request('dari')) && !empty(request('sampai')))
                    <span class="hidden sm:inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-white/15 text-white/90">
                        {{ $labelPeriode }}
                    </span>
                @endif
            </div>

            <!-- Dropdown Filter Tahun Langsung di Card -->
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-white/85 flex items-center gap-1">
                    <i class="ti ti-calendar text-xs"></i>
                    <span>Tahun:</span>
                </span>
                <div class="relative">
                    <select id="card_filter_tahun" 
                            onchange="updateYearFilter(this.value)"
                            class="appearance-none pl-3 pr-8 py-1.5 text-xs font-black rounded-xl bg-white/25 hover:bg-white/35 text-white border border-white/35 backdrop-blur-md shadow-xs focus:outline-none focus:ring-2 focus:ring-white/50 transition cursor-pointer">
                        <option value="all" class="text-slate-800 bg-white" {{ ($tahun ?? request('tahun', date('Y'))) === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                        @for ($y = date('Y') + 1; $y >= 2022; $y--)
                            <option value="{{ $y }}" class="text-slate-800 bg-white" {{ ($tahun ?? request('tahun', date('Y'))) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                    <i class="ti ti-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-white/80 text-xs pointer-events-none"></i>
                </div>
            </div>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            <!-- Segment 1: Total Pengajuan -->
            <div class="flex-1 min-w-[130px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Pengajuan
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Semua
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalLembur) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-file-text text-xs opacity-80"></i>
                        <span>Surat Lembur</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        100%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Menunggu Approval (Pending) -->
            <div class="flex-1 min-w-[130px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-amber-200 truncate">
                            Menunggu Approval
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400/25 text-amber-100 border border-amber-300/30">
                            Pending
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalPending) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-hourglass text-xs text-amber-300"></i>
                        <span>Belum Ditinjau</span>
                    </span>
                    <span class="font-black text-amber-200 text-[10px] bg-amber-500/20 px-2 py-0.5 rounded-md">
                        {{ $persenPending }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Disetujui (Approved) -->
            <div class="flex-1 min-w-[130px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Disetujui
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Approved
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalApproved) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-check text-xs opacity-80"></i>
                        <span>Disetujui</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $persenApproved }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 4: Ditolak (Rejected) -->
            <div class="flex-1 min-w-[130px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-rose-200 truncate">
                            Ditolak
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/25 text-rose-100 border border-rose-400/30">
                            Rejected
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalRejected) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-x text-xs text-rose-300"></i>
                        <span>Tidak Disetujui</span>
                    </span>
                    <span class="font-black text-rose-200 text-[10px] bg-rose-500/20 px-2 py-0.5 rounded-md">
                        {{ $persenRejected }}%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. FILTER BAR STANDAR V2 (2-BARIS KONSISTEN) ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('lembur.index') }}" method="GET" class="space-y-3">
            
            <!-- BARIS 1: PERIODE TANGGAL & NAMA KARYAWAN -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 sm:gap-3">
                <!-- Filter Tahun (Dropdown) -->
                <div class="md:col-span-3 relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar-event text-sm"></i>
                    </span>
                    <select name="tahun" id="filter_tahun" onchange="updateYearFilter(this.value)" class="w-full h-10 pl-9 pr-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-semibold shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                        <option value="all" {{ ($tahun ?? request('tahun', date('Y'))) === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                        @for ($y = date('Y') + 1; $y >= 2022; $y--)
                            <option value="{{ $y }}" {{ ($tahun ?? request('tahun', date('Y'))) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Dari Tanggal -->
                <div class="md:col-span-2 relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-sm"></i>
                    </span>
                    <input type="text" 
                           id="dari" 
                           name="dari" 
                           value="{{ request('dari') }}" 
                           placeholder="Dari Tanggal (Opsional)"
                           class="w-full h-10 pl-9 pr-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium flatpickr-date outline-none transition cursor-pointer">
                </div>

                <!-- Sampai Tanggal -->
                <div class="md:col-span-2 relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-sm"></i>
                    </span>
                    <input type="text" 
                           id="sampai" 
                           name="sampai" 
                           value="{{ request('sampai') }}" 
                           placeholder="Sampai Tanggal (Opsional)"
                           class="w-full h-10 pl-9 pr-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium flatpickr-date outline-none transition cursor-pointer">
                </div>

                <!-- Nama Karyawan Search -->
                <div class="md:col-span-5 relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-sm"></i>
                    </span>
                    <input type="text" 
                           name="nama_karyawan" 
                           value="{{ request('nama_karyawan') }}" 
                           placeholder="Cari Nama Karyawan..." 
                           autocomplete="off"
                           class="w-full h-10 pl-9 pr-4 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 outline-none transition">
                </div>
            </div>

            <!-- BARIS 2: STATUS, CABANG, DEPARTEMEN & TOMBOL FILTER -->
            <div class="flex flex-col sm:flex-row items-center gap-2.5 sm:gap-3 w-full">
                <!-- Status Filter -->
                <div class="w-full sm:w-1/4">
                    <select name="status" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending (Menunggu)</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Disetujui (Approved)</option>
                        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                    </select>
                </div>

                <!-- Cabang Filter -->
                <div class="w-full sm:w-1/3">
                    <select name="kode_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ textUpperCase($c->nama_cabang) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Departemen Filter -->
                <div class="w-full sm:w-1/3">
                    <select name="kode_dept" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                {{ textUpperCase($d->nama_dept) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Filter & Reset -->
                <div class="flex items-center gap-1.5 shrink-0 w-full sm:w-auto">
                    <button type="submit" 
                            class="w-full sm:w-auto px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-search text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['dari', 'sampai', 'nama_karyawan', 'status', 'kode_cabang', 'kode_dept']) || (request('tahun') && request('tahun') != date('Y')))
                        <a href="{{ route('lembur.index') }}" 
                           class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1 active:scale-95"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Divider with Counter Information -->
        <div class="mt-3.5 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
            <div class="text-slate-500 text-[11.5px]">
                Menampilkan <strong>{{ $lembur->firstItem() ?? 0 }} - {{ $lembur->lastItem() ?? 0 }}</strong> dari <strong>{{ $lembur->total() }}</strong> pengajuan lembur
                <span class="text-slate-300 mx-1.5">|</span>
                Pending: <strong class="text-amber-700 font-bold">{{ $totalPending }}</strong>
                <span class="text-slate-300 mx-1">|</span>
                Approved: <strong class="text-emerald-700 font-bold">{{ $totalApproved }}</strong>
                <span class="text-slate-300 mx-1">|</span>
                Rejected: <strong class="text-rose-600 font-bold">{{ $totalRejected }}</strong>
            </div>
            <div class="text-slate-400 text-[11px] font-medium">
                Pembaruan waktu otomatis real-time
            </div>
        </div>
    </div>

    <!-- ================= 4. LIST CARD DATA LEMBUR MODERN ================= -->
    <div class="space-y-3">
        @forelse ($lembur as $d)
            @php
                $start = strtotime($d->lembur_mulai);
                $end = strtotime($d->lembur_selesai);
                $diff = $end - $start;
                $hours = floor($diff / 3600);
                $minutes = floor(($diff % 3600) / 60);
                $duration = $hours . " Jam " . ($minutes > 0 ? $minutes . " Menit" : "");
                
                /* Realisasi Duration */
                $real_duration = "-";
                if ($d->lembur_in && $d->lembur_out) {
                    $real_duration = round(hitungJam($d->lembur_in, $d->lembur_out), 2) . " Jam";
                }
                $encryptedId = Crypt::encrypt($d->id);
            @endphp
            <div class="bg-white rounded-2xl border {{ $d->status === '1' ? 'border-slate-200/90 hover:border-emerald-300' : ($d->status === '2' ? 'border-rose-200/80 hover:border-rose-300' : 'border-amber-200/80 hover:border-amber-300') }} px-4 py-3.5 sm:px-5 sm:py-3.5 shadow-2xs hover:shadow-sm transition-all duration-200 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4">
                    
                    <!-- Left Section: Avatar, Identity, Overtime Chips & Details -->
                    <div class="flex items-center gap-3 sm:gap-3.5 min-w-0 flex-1">
                        
                        <!-- Row Index Badge -->
                        <div class="hidden sm:flex w-6 h-6 rounded-lg bg-slate-100 text-slate-500 text-[10.5px] font-bold items-center justify-center shrink-0 border border-slate-200/80">
                            {{ $loop->iteration + $lembur->firstItem() - 1 }}
                        </div>

                        <!-- Avatar Karyawan -->
                        <div class="relative shrink-0">
                            @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                <img src="{{ url('/storage/karyawan/' . $d->foto) }}" alt="{{ $d->nama_karyawan }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shadow-2xs">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-black text-xs shadow-2xs">
                                    {{ strtoupper(substr($d->nama_karyawan, 0, 2)) }}
                                </div>
                            @endif

                            @if($d->status === '1')
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[9px] ring-2 ring-white" title="Disetujui">
                                    <i class="ti ti-check"></i>
                                </span>
                            @elseif($d->status === '2')
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white flex items-center justify-center text-[9px] ring-2 ring-white" title="Ditolak">
                                    <i class="ti ti-x"></i>
                                </span>
                            @else
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-amber-500 text-white flex items-center justify-center text-[9px] ring-2 ring-white" title="Pending">
                                    <i class="ti ti-hourglass"></i>
                                </span>
                            @endif
                        </div>

                        <!-- Details Block (Compact & Streamlined) -->
                        <div class="min-w-0 flex-1 space-y-1">
                            <!-- Line 1: Nama, NIK, Jabatan, Dept, Cabang, Status -->
                            <div class="flex items-center flex-wrap gap-1.5 sm:gap-2">
                                <h3 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight group-hover:text-emerald-700 transition truncate">
                                    {{ $d->nama_karyawan }}
                                </h3>
                                
                                <span class="px-1.5 py-0.2 rounded text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $d->nik_show ?? $d->nik }}
                                </span>

                                <span class="text-slate-400 text-xs hidden sm:inline">&bull;</span>

                                <span class="text-[11px] font-medium text-slate-600 truncate max-w-[140px]" title="{{ $d->nama_jabatan }}">
                                    {{ $d->nama_jabatan ?? '-' }}
                                </span>

                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $d->nama_dept ?? '-' }}
                                </span>

                                <span class="px-1.5 py-0.2 rounded text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200/80">
                                    {{ $d->nama_cabang }}
                                </span>

                                <!-- Status Badge Pill -->
                                @if ($d->status === '0')
                                    <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs inline-flex items-center gap-1">
                                        <i class="ti ti-hourglass text-[11px]"></i> Pending
                                    </span>
                                @elseif ($d->status === '1')
                                    <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs inline-flex items-center gap-1">
                                        <i class="ti ti-check text-[11px]"></i> Disetujui
                                    </span>
                                @elseif ($d->status === '2')
                                    <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs inline-flex items-center gap-1">
                                        <i class="ti ti-x text-[11px]"></i> Ditolak
                                    </span>
                                @endif
                            </div>

                            <!-- Line 2: Jadwal Lembur, Rencana & Realisasi Chips -->
                            <div class="flex items-center flex-wrap gap-1.5 sm:gap-2 text-xs">
                                <div class="inline-flex items-center gap-1 text-[11.5px] text-slate-700 font-bold">
                                    <i class="ti ti-calendar text-emerald-600 text-xs"></i>
                                    <span>{{ DateToIndo($d->tanggal) }}</span>
                                </div>

                                <span class="text-slate-300 text-xs hidden sm:inline">|</span>

                                <!-- Waktu Rencana -->
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 text-sky-900 border border-sky-200 text-[11px] font-medium">
                                    <i class="ti ti-clock text-sky-600 text-xs"></i>
                                    <span class="text-slate-500">Rencana:</span>
                                    <span class="text-sky-800 font-mono font-bold">{{ date('H:i', $start) }} - {{ date('H:i', $end) }}</span>
                                    <span class="px-1 rounded bg-sky-200/70 text-sky-900 text-[10px] font-bold">{{ $duration }}</span>
                                </div>

                                <!-- Realisasi Absensi Lembur -->
                                @if($d->lembur_in && $d->lembur_out)
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-900 border border-emerald-200 text-[11px] font-medium">
                                        <i class="ti ti-fingerprint text-emerald-600 text-xs"></i>
                                        <span class="text-slate-500">Realisasi:</span>
                                        <span class="text-emerald-800 font-mono font-bold">{{ date('H:i', strtotime($d->lembur_in)) }} - {{ date('H:i', strtotime($d->lembur_out)) }}</span>
                                        <span class="px-1 rounded bg-emerald-200/70 text-emerald-900 text-[10px] font-bold">{{ $real_duration }}</span>
                                    </div>
                                @elseif($d->lembur_in)
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-200 text-[11px] font-semibold">
                                        <i class="ti ti-clock-play text-amber-600 text-xs"></i>
                                        <span>IN: {{ date('H:i', strtotime($d->lembur_in)) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Action Buttons Toolbar -->
                    <div class="flex items-center gap-1 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100 w-full lg:w-auto justify-end">
                        
                        <!-- Show Detail & Bukti Realisasi -->
                        <button type="button" 
                                class="btnShowLembur w-8 h-8 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs" 
                                data-id="{{ $encryptedId }}" 
                                title="Lihat Detail & Foto Lembur">
                            <i class="ti ti-eye text-sm"></i>
                        </button>

                        <!-- Print SPL -->
                        <a href="{{ (\Illuminate\Support\Facades\Route::has('lembur.print') ? route('lembur.print', $encryptedId) : '#') }}" target="_blank"
                           class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs"
                           title="Cetak Surat Perintah Lembur (SPL)">
                            <i class="ti ti-printer text-sm"></i>
                        </a>

                        @if ($d->status === '0')
                            @can('lembur.approve')
                                <button type="button" 
                                        class="btnApproveLembur inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition active:scale-95 cursor-pointer"
                                        data-id="{{ $encryptedId }}">
                                    <i class="ti ti-check text-sm"></i>
                                    <span>Persetujuan</span>
                                </button>
                            @endcan

                            @can('lembur.edit')
                                <button type="button" 
                                        class="btnEditLembur w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs"
                                        data-id="{{ $encryptedId }}"
                                        title="Edit Data Lembur">
                                    <i class="ti ti-edit text-sm"></i>
                                </button>
                            @endcan

                            @can('lembur.delete')
                                <form action="{{ route('lembur.delete', $encryptedId) }}" method="POST" class="formDelete inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs"
                                            title="Hapus Data">
                                        <i class="ti ti-trash text-sm"></i>
                                    </button>
                                </form>
                            @endcan
                        @else
                            @can('lembur.approve')
                                <form action="{{ route('lembur.cancelapprove', $encryptedId) }}" method="POST" class="formCancelApprove inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-rose-50 hover:text-rose-700 text-slate-600 font-bold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                                            title="Batalkan Status Approval">
                                        <i class="ti ti-rotate-2 text-sm text-rose-500"></i>
                                        <span>Batalkan</span>
                                    </button>
                                </form>
                            @endcan
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/90 p-12 text-center shadow-2xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                    <i class="ti ti-clock-off"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800 mb-1">Tidak Ada Data Lembur</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
                    Belum ada pengajuan lembur yang tercatat sesuai filter yang dipilih.
                </p>
                @can('lembur.create')
                    <button type="button" onclick="$('#btnCreateLembur').click();" 
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition hover:opacity-95 active:scale-95 cursor-pointer"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Buat Surat Lembur Sekarang</span>
                    </button>
                @endcan
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($lembur->hasPages())
        <div class="pt-2">
            {{ $lembur->links('v2.components.pagination') }}
        </div>
    @endif

</div>

<!-- ================= 5. MODAL DIALOG STANDAR V2 (KONSISTEN DENGAN MODAL MASTER DATA V2) ================= -->
<div class="modal fade" id="modalLembur" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden bg-white">
            <!-- Modal Header Konsisten V2 -->
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-clock-play"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalLemburTitle">Formulir Lembur</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalLemburSubtitle">Kelola informasi pengajuan lembur karyawan</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>

            <!-- Modal Body Container (AJAX Loaded) -->
            <div class="p-6 bg-white" id="modalLemburBody">
                <div class="py-12 text-center text-slate-400">
                    <div style="width: 24px; height: 24px; border: 3px solid {{ $themeColor1 }}; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                    <p class="text-xs font-semibold text-slate-500 mt-2">Memuat formulir...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
// Update Year Filter instantly
window.updateYearFilter = function(year) {
    const url = new URL(window.location.href);
    url.searchParams.set('tahun', year);
    url.searchParams.delete('dari');
    url.searchParams.delete('sampai');
    url.searchParams.delete('page');
    window.location.href = url.toString();
};

$(document).ready(function() {
    // Inisialisasi Flatpickr Native
    if (typeof flatpickr !== 'undefined') {
        const elDari = document.getElementById('dari');
        const elSampai = document.getElementById('sampai');
        if (elDari) {
            flatpickr(elDari, {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y'
            });
        }
        if (elSampai) {
            flatpickr(elSampai, {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y'
            });
        }
    }

    const modalLemburEl = document.getElementById('modalLembur');
    const modalLembur = new bootstrap.Modal(modalLemburEl);

    function showLoading() {
        $('#modalLemburBody').html(`
            <div class="py-12 text-center text-slate-400">
                <div style="width: 28px; height: 28px; border: 3px solid {{ $themeColor1 }}; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <p class="text-xs font-semibold text-slate-500 mt-2">Memuat data formulir...</p>
            </div>
        `);
    }

    // 1. Create Lembur
    $('#btnCreateLembur').click(function() {
        $('#modalLemburTitle').text('Buat Surat Perintah Lembur');
        $('#modalLemburSubtitle').text('Tambahkan jadwal pengajuan lembur untuk karyawan terkait');
        showLoading();
        modalLembur.show();

        $('#modalLemburBody').load("{{ route('lembur.create') }}");
    });

    // 2. Edit Lembur
    $('.btnEditLembur').click(function() {
        const id = $(this).data('id');
        $('#modalLemburTitle').text('Edit Data Lembur');
        $('#modalLemburSubtitle').text('Perbarui waktu atau koreksi presensi pengajuan lembur');
        showLoading();
        modalLembur.show();

        $('#modalLemburBody').load(`/lembur/${id}/edit`);
    });

    // 3. Show Detail Lembur
    $('.btnShowLembur').click(function() {
        const id = $(this).data('id');
        $('#modalLemburTitle').text('Detail & Realisasi Lembur');
        $('#modalLemburSubtitle').text('Informasi waktu, absensi, dan bukti foto lembur');
        showLoading();
        modalLembur.show();

        $('#modalLemburBody').load(`/lembur/${id}/show`);
    });

    // 4. Approve Lembur Modal
    $('.btnApproveLembur').click(function() {
        const id = $(this).data('id');
        $('#modalLemburTitle').text('Persetujuan Lembur');
        $('#modalLemburSubtitle').text('Tinjau rincian lembur untuk disetujui atau ditolak');
        showLoading();
        modalLembur.show();

        $('#modalLemburBody').load(`/lembur/${id}/approve`);
    });

    // 5. Delete Lembur Confirmation
    $('.formDelete').submit(function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Data Lembur?',
            text: 'Data lembur ini akan dihapus secara permanen dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl shadow-xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // 6. Cancel Approve Confirmation
    $('.formCancelApprove').submit(function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Batalkan Persetujuan?',
            text: 'Status lembur ini akan dikembalikan ke status Pending (Menunggu).',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Kembali',
            customClass: { popup: 'rounded-2xl shadow-xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
