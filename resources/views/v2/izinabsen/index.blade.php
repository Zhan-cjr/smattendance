@extends('v2.layouts.app')

@section('titlepage', 'Izin Absen')
@section('page_title', 'Pengajuan Izin Absen')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    
    $totalPending = $stats['pending'] ?? 0;
    $totalDisetujui = $stats['disetujui'] ?? 0;
    $totalDitolak = $stats['ditolak'] ?? 0;
@endphp

<div class="space-y-4" x-data="{ viewMode: 'list' }">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-file-description text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Pengajuan Izin Absen</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola permohonan izin tidak masuk kerja, verifikasi approval berjenjang, dan riwayat presensi
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
                    <i class="ti ti-folder-check text-sm"></i>
                    <span>Pengajuan</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Izin Absen</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('izinabsen.create')
                    <!-- Tambah Data -->
                    <button type="button" id="btnCreate" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Izin</span>
                    </button>
                @endcan

                @can('approvallayer.index')
                    <!-- Konfigurasi Approval -->
                    <a href="{{ route('approvallayer.index') }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95">
                        <i class="ti ti-settings text-slate-500 text-sm"></i>
                        <span>Konfigurasi Approval</span>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. STATUS METRICS (MENUNGGU, DISETUJUI, DITOLAK) ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-file-description text-[200px]"></i>
        </div>

        <!-- Period & Year Selector Dropdown on the Card -->
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-3 pb-3 mb-4 border-b border-white/15">
            <div class="flex items-center gap-2">
                <i class="ti ti-chart-bar text-white/80 text-base"></i>
                <span class="text-xs sm:text-sm font-bold text-white tracking-wide">Statistik Pengajuan</span>
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
            <!-- Segment 1: Menunggu Approval (Pending) -->
            <div class="flex-1 min-w-[140px] sm:min-w-[160px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-amber-100 truncate">
                            Menunggu Approval
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400/30 text-amber-100 border border-amber-300/40">
                            Pending
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-amber-200 tracking-tight mt-1.5">
                        {{ number_format($totalPending) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-clock-pause text-xs opacity-80"></i>
                        <span>Butuh Persetujuan</span>
                    </span>
                    <span class="font-black text-amber-100 text-[10px] bg-amber-400/20 px-2 py-0.5 rounded-md">
                        Antrean
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Disetujui (Approved) -->
            <div class="flex-1 min-w-[140px] sm:min-w-[160px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Disetujui
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-400/30 text-emerald-100 border border-emerald-300/40">
                            Approved
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalDisetujui) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-circle-check text-xs opacity-80"></i>
                        <span>Izin Diterima</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/20 px-2 py-0.5 rounded-md">
                        Aktif
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Ditolak (Rejected) -->
            <div class="flex-1 min-w-[140px] sm:min-w-[160px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-rose-100 truncate">
                            Ditolak
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-400/30 text-rose-100 border border-rose-300/40">
                            Rejected
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-rose-100 tracking-tight mt-1.5">
                        {{ number_format($totalDitolak) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-circle-x text-xs opacity-80"></i>
                        <span>Permohonan Ditolak</span>
                    </span>
                    <span class="font-black text-rose-100 text-[10px] bg-rose-400/20 px-2 py-0.5 rounded-md">
                        Ditolak
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. NAVIGATION TAB MENU (MODERN V2) ================= -->
    <div class="bg-slate-100/70 p-1.5 rounded-2xl border border-slate-200/80">
        @include('v2.components.navigation.nav_pengajuan_absen')
    </div>

    <!-- ================= 4. FILTER & SEARCH TOOLBAR (KONSISTEN V2) ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('izinabsen.index') }}" method="GET" class="w-full space-y-2.5 sm:space-y-3">
            
            <!-- BARIS 1: PERIODE TANGGAL & TAHUN -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 sm:gap-3 w-full">
                <!-- Filter Tahun (Dropdown) -->
                <div class="sm:col-span-4 relative">
                    <i class="ti ti-calendar-event absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none z-10"></i>
                    <select name="tahun" id="filter_tahun" onchange="updateYearFilter(this.value)" class="w-full pl-11 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-semibold shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                        <option value="all" {{ ($tahun ?? request('tahun', date('Y'))) === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                        @for ($y = date('Y') + 1; $y >= 2022; $y--)
                            <option value="{{ $y }}" {{ ($tahun ?? request('tahun', date('Y'))) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Dari Tanggal -->
                <div class="sm:col-span-4 relative">
                    <i class="ti ti-calendar absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none z-10"></i>
                    <input type="text" 
                           id="filter_dari" 
                           name="dari" 
                           value="{{ request('dari') }}" 
                           placeholder="Dari Tanggal (Opsional)" 
                           class="w-full pl-11 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-semibold placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                </div>

                <!-- Sampai Tanggal -->
                <div class="sm:col-span-4 relative">
                    <i class="ti ti-calendar absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none z-10"></i>
                    <input type="text" 
                           id="filter_sampai" 
                           name="sampai" 
                           value="{{ request('sampai') }}" 
                           placeholder="Sampai Tanggal (Opsional)" 
                           class="w-full pl-11 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-semibold placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                </div>
            </div>

            <!-- BARIS 2: SEARCH NAMA/NIK, SELECT STATUS, CABANG, DEPARTEMEN & TOMBOL AKSI -->
            <div class="flex flex-col lg:flex-row items-center gap-2.5 sm:gap-3 w-full">
                
                <!-- Search Input Nama / NIK (Expanding flex-1) -->
                <div class="flex-1 min-w-0 w-full relative">
                    <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none"></i>
                    <input type="text" 
                           name="nama_karyawan" 
                           value="{{ request('nama_karyawan') }}" 
                           placeholder="Cari Nama Karyawan / NIK..." 
                           class="w-full pl-11 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-auto lg:w-36 shrink-0">
                    <select name="status" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Disetujui</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Cabang Filter -->
                <div class="w-full sm:w-auto lg:w-44 shrink-0">
                    <select name="kode_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Departemen Filter -->
                <div class="w-full sm:w-auto lg:w-44 shrink-0">
                    <select name="kode_dept" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Dept</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons: Cari & Reset -->
                <div class="flex items-center gap-1.5 shrink-0 w-full lg:w-auto">
                    <button type="submit" 
                            class="w-full lg:w-auto px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-search text-sm"></i>
                        <span>Cari</span>
                    </button>
                    @if(request()->hasAny(['dari', 'sampai', 'nama_karyawan', 'status', 'kode_cabang', 'kode_dept']) || (request('tahun') && request('tahun') != date('Y')))
                        <a href="{{ route('izinabsen.index') }}" 
                           class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1 active:scale-95"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    <!-- ================= 5. DAFTAR KARTU PENGAJUAN IZIN ABSEN (COMPACT & RAPI V2) ================= -->
    <div class="space-y-2">
        @forelse ($izinabsen as $d)
            @php
                $lama = hitungHariKerja($d->nik, $d->dari, $d->sampai)['total_hari'];
                $path = Storage::url('karyawan/' . $d->foto);
                $encryptedKode = Crypt::encrypt($d->kode_izin);
                $nextLayer = $d->getNextApprovalLayer();
                $userRole = auth()->user()->getRoleNames()->first();
                
                $canApprove = false;
                if (auth()->user()->hasRole('super admin') || ($nextLayer && $nextLayer->role_name == $userRole)) {
                    $canApprove = true;
                }

                $canCancel = false;
                if ($d->approval_step > 1) {
                    $lastStep = $d->approval_step - 1;
                    $lastApproval = $d->approvals->where('level', $lastStep)->where('user_id', auth()->id())->first();
                    if ($lastApproval) {
                        $canCancel = true;
                    }
                }
            @endphp

            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs hover:shadow-sm hover:border-slate-300 transition-all duration-200 overflow-hidden group">
                <div class="p-2.5 sm:p-3">
                    
                    <!-- BARIS 1: IDENTITAS KARYAWAN & STATUS / AKSI (COMPACT & CLEAN) -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-slate-100">
                        
                        <!-- Left: Avatar Foto & Info Pegawai -->
                        <div class="flex items-center gap-2.5 min-w-0">
                            <!-- Avatar Foto -->
                            <div class="relative shrink-0">
                                @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                    <img src="{{ getfotoKaryawan($d->foto) }}" alt="{{ $d->nama_karyawan }}" class="w-8 h-9 rounded-lg object-cover border border-slate-200 shadow-2xs">
                                @else
                                    <div class="w-8 h-9 rounded-lg bg-emerald-600 text-white border border-emerald-700/30 flex items-center justify-center font-black text-xs shadow-2xs">
                                        {{ strtoupper(substr($d->nama_karyawan, 0, 1)) }}
                                    </div>
                                @endif

                                @if($d->status == 1)
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[8px] ring-1.5 ring-white shadow-2xs" title="Disetujui">
                                        <i class="ti ti-check"></i>
                                    </span>
                                @elseif($d->status == 2)
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-rose-500 text-white flex items-center justify-center text-[8px] ring-1.5 ring-white shadow-2xs" title="Ditolak">
                                        <i class="ti ti-x"></i>
                                    </span>
                                @else
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-amber-500 text-white flex items-center justify-center text-[8px] ring-1.5 ring-white shadow-2xs" title="Pending">
                                        <i class="ti ti-clock"></i>
                                    </span>
                                @endif
                            </div>

                            <!-- Nama, NIK, Jabatan, Dept, Cabang -->
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h6 class="font-bold text-slate-900 text-xs sm:text-[13px] mb-0 group-hover:text-emerald-700 transition truncate max-w-[200px] sm:max-w-xs md:max-w-md">
                                        {{ $d->nama_karyawan }}
                                    </h6>
                                    <span class="font-mono text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                        {{ $d->nik_show ?? $d->nik }}
                                    </span>
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-slate-600 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200/80">
                                        <i class="ti ti-briefcase text-[10px] text-slate-400"></i> {{ $d->nama_jabatan }}
                                    </span>
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-slate-600 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200/80">
                                        <i class="ti ti-building text-[10px] text-slate-400"></i> {{ $d->nama_dept }}
                                    </span>
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-slate-600 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200/80">
                                        <i class="ti ti-map-pin text-[10px] text-slate-400"></i> {{ $d->nama_cabang }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Status Badge & Action Buttons -->
                        <div class="flex items-center gap-1.5 justify-between sm:justify-end shrink-0">
                            
                            <!-- Status Approval Badge -->
                            <div>
                                @if ($d->status == 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="ti ti-hourglass-empty text-[11px]"></i>
                                        <span>Pending</span>
                                        @if ($nextLayer)
                                            <span class="text-amber-500 font-normal">({{ $nextLayer->role_name }})</span>
                                        @endif
                                    </span>
                                @elseif ($d->status == 1)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ti ti-circle-check text-[11px]"></i> Disetujui
                                    </span>
                                @elseif ($d->status == 2)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="ti ti-circle-x text-[11px]"></i> Ditolak
                                    </span>
                                @endif
                            </div>

                            <!-- Grouped Action Buttons -->
                            <div class="inline-flex items-center border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                @can('izinabsen.approve')
                                    @if ($d->status == 0 && $canApprove)
                                        <!-- Tombol Proses Approval -->
                                        <button type="button" 
                                                class="btnApprove px-2 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition flex items-center gap-1 cursor-pointer active:scale-95" 
                                                kode_izin="{{ $encryptedKode }}"
                                                title="Proses Approval Izin">
                                            <i class="ti ti-external-link text-xs"></i>
                                            <span>Proses</span>
                                        </button>
                                    @endif

                                    @if ($d->status == 0 && $canCancel)
                                        <!-- Batalkan Approval Tingkat Sebelumnya -->
                                        <form method="POST" action="{{ route('izinabsen.cancelapprove', $encryptedKode) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="cancel-confirm w-6 h-6 rounded flex items-center justify-center text-amber-600 hover:bg-amber-100 transition cursor-pointer"
                                                    title="Batalkan Approval Terakhir">
                                                <i class="ti ti-arrow-back-up text-xs"></i>
                                            </button>
                                        </form>
                                    @elseif ($d->status == 1 || $d->status == 2)
                                        <!-- Batalkan Keputusan Final -->
                                        <form method="POST" action="{{ route('izinabsen.cancelapprove', $encryptedKode) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="cancel-confirm w-6 h-6 rounded flex items-center justify-center text-rose-600 hover:bg-rose-100 transition cursor-pointer"
                                                    title="Batalkan Keputusan (Revert)">
                                                <i class="ti ti-circle-minus text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan

                                @can('izinabsen.edit')
                                    @if ($d->status == 0)
                                        <button type="button" 
                                                class="btnEdit w-6 h-6 rounded flex items-center justify-center text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition cursor-pointer"
                                                kode_izin="{{ $encryptedKode }}"
                                                title="Edit Izin">
                                            <i class="ti ti-edit text-xs"></i>
                                        </button>
                                    @endif
                                @endcan

                                @can('izinabsen.index')
                                    <button type="button" 
                                            class="btnShow w-6 h-6 rounded flex items-center justify-center text-slate-600 hover:text-sky-700 hover:bg-sky-50 transition cursor-pointer"
                                            kode_izin="{{ $encryptedKode }}"
                                            title="Detail Izin">
                                        <i class="ti ti-file-description text-xs"></i>
                                    </button>
                                @endcan

                                @can('izinabsen.delete')
                                    @if ($d->status == 0)
                                        <form method="POST" action="{{ route('izinabsen.delete', $encryptedKode) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="delete-confirm w-6 h-6 rounded flex items-center justify-center text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition cursor-pointer"
                                                    title="Hapus Izin">
                                                <i class="ti ti-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>

                        </div>
                    </div>

                    <!-- BARIS 2: RINCIAN IZIN DENGAN SEKAT DASHED (KODE, PERIODE, DURASI & ALASAN) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2 sm:gap-3 items-center pt-2 text-xs">
                        
                        <!-- Col 1: Kode Izin & Tanggal Dibuat -->
                        <div class="lg:col-span-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 text-xs">
                                <i class="ti ti-barcode"></i>
                            </span>
                            <div class="min-w-0">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block leading-none">Kode Izin</span>
                                <span class="font-mono text-xs font-bold text-slate-800 leading-tight block mt-0.5">{{ $d->kode_izin }}</span>
                            </div>
                        </div>

                        <!-- Dashed Divider -->
                        <div class="hidden lg:block w-[1px] h-6 border-r border-dashed border-slate-200"></div>

                        <!-- Col 2: Periode Rentang Tanggal -->
                        <div class="lg:col-span-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-xs">
                                <i class="ti ti-calendar"></i>
                            </span>
                            <div class="min-w-0">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block leading-none">Periode Tanggal</span>
                                <span class="font-semibold text-slate-800 text-xs leading-tight block mt-0.5">
                                    {{ date('d/m/Y', strtotime($d->dari)) }} <span class="text-slate-400 font-normal">s/d</span> {{ date('d/m/Y', strtotime($d->sampai)) }}
                                </span>
                            </div>
                        </div>

                        <!-- Dashed Divider -->
                        <div class="hidden lg:block w-[1px] h-6 border-r border-dashed border-slate-200"></div>

                        <!-- Col 3: Durasi Hari Kerja -->
                        <div class="lg:col-span-2 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-xs">
                                <i class="ti ti-calendar-time"></i>
                            </span>
                            <div class="min-w-0">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block leading-none">Durasi</span>
                                <span class="font-bold text-emerald-700 text-xs leading-tight block mt-0.5">
                                    {{ $lama }} Hari Kerja
                                </span>
                            </div>
                        </div>

                        <!-- Dashed Divider -->
                        <div class="hidden lg:block w-[1px] h-6 border-r border-dashed border-slate-200"></div>

                        <!-- Col 4: Alasan / Keterangan Izin -->
                        <div class="lg:col-span-3 flex items-center gap-2 min-w-0">
                            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 text-xs">
                                <i class="ti ti-notes"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block leading-none">Alasan / Catatan</span>
                                <span class="text-xs text-slate-600 italic truncate block mt-0.5" title="{{ $d->keterangan }}">
                                    "{{ $d->keterangan ?? '-' }}"
                                </span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/80 p-8 sm:p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-3xl mx-auto mb-3">
                    <i class="ti ti-file-x"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Tidak Ada Data Izin Absen</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Belum ada pengajuan izin absen yang sesuai dengan filter atau periode tanggal yang dipilih.
                </p>
            </div>
        @endforelse
    </div>

    <!-- ================= 6. PAGINATION ================= -->
    <div class="pt-2">
        {{ $izinabsen->links('v2.components.pagination') }}
    </div>

</div>

<!-- ================= 7. MODAL DIALOG V2 ================= -->
<div class="modal fade" id="modalDialogV2" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border border-slate-200/90 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-file-description"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalTitleV2">Pengajuan Izin</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalSubtitleV2">Kelola pengajuan izin tidak masuk kerja</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6" id="modalBodyV2">
                <!-- Dynamic Content Loaded Via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    window.updateYearFilter = function(year) {
        const url = new URL(window.location.href);
        url.searchParams.set('tahun', year);
        url.searchParams.delete('dari');
        url.searchParams.delete('sampai');
        url.searchParams.delete('page');
        window.location.href = url.toString();
    };

    (function() {
        // Init Flatpickr Filter
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#filter_dari", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y"
            });
            flatpickr("#filter_sampai", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y"
            });
        }

        function showLoadingModal() {
            $('#modalBodyV2').html(`
                <div class="py-10 text-center">
                    <div style="width: 28px; height: 28px; border: 3px solid #10b981; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                    <p class="text-xs font-semibold text-slate-500 mt-2.5">Memuat data...</p>
                </div>
            `);
        }

        // 1. Create Modal
        $('#btnCreate').click(function() {
            $('#modalTitleV2').text('Buat Pengajuan Izin Absen');
            $('#modalDialogV2').modal('show');
            showLoadingModal();
            $('#modalBodyV2').load('{{ route('izinabsen.create') }}');
        });

        // 2. Approve Modal
        $(document).on('click', '.btnApprove', function() {
            const kode_izin = $(this).attr('kode_izin');
            $('#modalTitleV2').text('Verifikasi & Approval Izin Absen');
            $('#modalDialogV2').modal('show');
            showLoadingModal();
            $('#modalBodyV2').load(`/izinabsen/${kode_izin}/approve`);
        });

        // 3. Show / Detail Modal
        $(document).on('click', '.btnShow', function() {
            const kode_izin = $(this).attr('kode_izin');
            $('#modalTitleV2').text('Detail Pengajuan Izin Absen');
            $('#modalDialogV2').modal('show');
            showLoadingModal();
            $('#modalBodyV2').load(`/izinabsen/${kode_izin}/show`);
        });

        // 4. Edit Modal
        $(document).on('click', '.btnEdit', function() {
            const kode_izin = $(this).attr('kode_izin');
            $('#modalTitleV2').text('Edit Pengajuan Izin Absen');
            $('#modalDialogV2').modal('show');
            showLoadingModal();
            $('#modalBodyV2').load(`/izinabsen/${kode_izin}/edit`);
        });

        // 5. SweetAlert Confirmations (Delete & Cancel)
        $(document).on('click', '.delete-confirm', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Pengajuan Izin?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $(document).on('click', '.cancel-confirm', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Batalkan Status Approval?',
                text: 'Status pengajuan akan dikembalikan ke tahap sebelumnya.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    })();
</script>
@endpush
