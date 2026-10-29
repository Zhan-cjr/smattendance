@extends('v2.layouts.app')

@section('titlepage', 'Data Karyawan')
@section('page_title', 'Data Karyawan')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    
    $totalKaryawan = $stats['total_karyawan'] ?? $karyawan->total();
    $totalAktif = $stats['aktif'] ?? 0;
    $totalNonaktif = $stats['nonaktif'] ?? 0;
    $totalTetap = $stats['tetap'] ?? 0;
    $totalKontrak = $stats['kontrak'] ?? 0;
    $persenAktif = $totalKaryawan > 0 ? round(($totalAktif / $totalKaryawan) * 100) : 0;
    $persenNonaktif = $totalKaryawan > 0 ? round(($totalNonaktif / $totalKaryawan) * 100) : 0;
    $persenTetap = $totalKaryawan > 0 ? round(($totalTetap / $totalKaryawan) * 100) : 0;
    $persenKontrak = $totalKaryawan > 0 ? round(($totalKontrak / $totalKaryawan) * 100) : 0;
@endphp

<div class="space-y-5" x-data="{ viewMode: 'list' }">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-id-badge-2 text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Karyawan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Manajemen data pegawai, jabatan, unit penempatan, jadwal kerja, dan akun akses sistem
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
                    <i class="ti ti-database text-sm"></i>
                    <span>Master Data</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Karyawan</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('karyawan.create')
                    <!-- Tambah Karyawan -->
                    <button type="button" id="btnCreate" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Karyawan</span>
                    </button>

                    <!-- Export Excel -->
                    <a href="{{ route('karyawan.export', request()->query()) }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95">
                        <i class="ti ti-file-spreadsheet text-emerald-600 text-sm"></i>
                        <span>Export</span>
                    </a>

                    <!-- Import Excel -->
                    <button type="button" id="btnImport" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                        <i class="ti ti-upload text-sky-600 text-sm"></i>
                        <span>Import</span>
                    </button>

                    <!-- More Ops (Dropdown) -->
                    @can('users.create')
                    <div class="relative" x-data="{ openMore: false }" @click.outside="openMore = false">
                        <button @click="openMore = !openMore" 
                                type="button"
                                class="inline-flex items-center gap-1 px-2.5 py-2 bg-white border border-slate-300/90 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold shadow-2xs transition"
                                title="Operasi Akun Massal">
                            <i class="ti ti-dots text-base"></i>
                        </button>
                        <div x-show="openMore" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-40 text-xs">
                            <a href="{{ route('karyawan.generatealluser') }}" 
                               class="flex items-center gap-2 px-3.5 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium">
                                <i class="ti ti-user-plus text-amber-500 text-sm"></i>
                                <span>Buat Semua User</span>
                            </a>
                            <a href="{{ route('karyawan.deletealluser') }}" 
                               class="delete-all-user flex items-center gap-2 px-3.5 py-2 text-rose-600 hover:bg-rose-50 transition font-medium border-t border-slate-100">
                                <i class="ti ti-user-x text-rose-500 text-sm"></i>
                                <span>Reset Semua User</span>
                            </a>
                        </div>
                    </div>
                    @endcan
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. EXECUTIVE STATISTICS (SINGLE SEAMLESS SOLID/GRADIENT CARD WITH TAPERED DIVIDERS - SEPERTI SIPRENPAS) ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none">
            <i class="ti ti-id-badge-2 text-[200px]"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            
            <!-- Segment 1: Total Karyawan -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Karyawan
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Semua
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalKaryawan) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px]">Semua Pegawai</span>
                    <span class="font-black text-white text-[10px] bg-white/20 px-2 py-0.5 rounded-md">
                        100%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Karyawan Aktif -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Karyawan Aktif
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/15 text-emerald-100 border border-white/20">
                            Aktif
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalAktif) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-user-check text-xs opacity-80"></i>
                        <span>Status Bekerja</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $persenAktif }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Karyawan Nonaktif -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Nonaktif / Off
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/15 text-emerald-100 border border-white/20">
                            Off
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalNonaktif) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-user-x text-xs opacity-80"></i>
                        <span>Status Berhenti</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $persenNonaktif }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 4: Pegawai Tetap (PKWTT) -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Pegawai Tetap
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/15 text-emerald-100 border border-white/20">
                            PKWTT
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalTetap) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-shield-check text-xs opacity-80"></i>
                        <span>Status Tetap</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $persenTetap }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 5: Pegawai Kontrak (PKWT) -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Pegawai Kontrak
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/15 text-emerald-100 border border-white/20">
                            PKWT
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalKontrak) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-clock text-xs opacity-80"></i>
                        <span>Status Kontrak</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $persenKontrak }}%
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= 3. FILTER TOOLBAR (FULL WIDTH, EXPANDING SEARCH, NO OVERLAP) ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('karyawan.index') }}" method="GET" class="w-full">
            <div class="flex flex-col lg:flex-row items-center gap-2.5 sm:gap-3 w-full">
                
                <!-- Search Input (Icon separated with pl-11, completely fixing overlap!) -->
                <div class="flex-1 min-w-0 w-full relative">
                    <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none"></i>
                    <input type="text" 
                           name="nama_karyawan" 
                           value="{{ request('nama_karyawan') }}" 
                           placeholder="Cari Nama Karyawan / NIK..." 
                           class="w-full pl-11 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <!-- Cabang Filter -->
                <div class="w-full sm:w-auto lg:w-44 shrink-0">
                    <select name="kode_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
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
                    <select name="kode_dept" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                        <option value="">Semua Dept</option>
                        @foreach ($departemen as $dept)
                            <option value="{{ $dept->kode_dept }}" {{ request('kode_dept') == $dept->kode_dept ? 'selected' : '' }}>
                                {{ $dept->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jabatan Filter -->
                <div class="w-full sm:w-auto lg:w-44 shrink-0">
                    <select name="kode_jabatan" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                        <option value="">Semua Jabatan</option>
                        @foreach ($jabatan as $j)
                            <option value="{{ $j->kode_jabatan }}" {{ request('kode_jabatan') == $j->kode_jabatan ? 'selected' : '' }}>
                                {{ $j->nama_jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort By -->
                <div class="w-full sm:w-auto lg:w-36 shrink-0">
                    <select name="sort_by" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                        <option value="nama_karyawan" {{ request('sort_by') == 'nama_karyawan' ? 'selected' : '' }}>Urut: Nama</option>
                        <option value="nik_show" {{ request('sort_by') == 'nik_show' ? 'selected' : '' }}>Urut: NIK</option>
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
                    @if(request()->hasAny(['nama_karyawan', 'kode_cabang', 'kode_dept', 'kode_jabatan', 'sort_by']))
                        <a href="{{ route('karyawan.index') }}" 
                           class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <!-- Divider with Counter & View Mode Switcher -->
        <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
            <div class="text-slate-500 text-[11.5px]">
                Menampilkan <strong>{{ $karyawan->firstItem() ?? 0 }} - {{ $karyawan->lastItem() ?? 0 }}</strong> dari <strong>{{ $karyawan->total() }}</strong> pegawai 
                <span class="text-slate-300 mx-1.5">|</span>
                Aktif: <strong class="text-emerald-700 font-bold">{{ $totalAktif }}</strong>
                <span class="text-slate-300 mx-1">|</span>
                Nonaktif: <strong class="text-rose-600 font-bold">{{ $totalNonaktif }}</strong>
            </div>

            <!-- View Switcher: Kartu List (Siprenpas style) vs Tabel -->
            <div class="flex items-center bg-slate-100 p-0.5 rounded-lg border border-slate-200/80">
                <button type="button" 
                        @click="viewMode = 'list'; $nextTick(() => window.initTooltips && window.initTooltips())"
                        :class="viewMode === 'list' ? 'bg-white text-slate-800 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800'"
                        class="flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] transition">
                    <i class="ti ti-layout-list text-sm"></i>
                    <span>Kartu List</span>
                </button>
                <button type="button" 
                        @click="viewMode = 'table'; $nextTick(() => window.initTooltips && window.initTooltips())"
                        :class="viewMode === 'table' ? 'bg-white text-slate-800 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800'"
                        class="flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] transition">
                    <i class="ti ti-table text-sm"></i>
                    <span>Tabel</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ================= 4. EMPLOYEE LIST: KARTU LIST VIEW (EXACT SIPRENPAS STYLE) ================= -->
    <div class="space-y-3" x-show="viewMode === 'list'">
        @forelse ($karyawan as $d)
            @php
                $awal = new DateTime($d->tanggal_masuk);
                $akhir = new DateTime();
                $masa_kerja = $akhir->diff($awal);
                $status_karyawan_text = $d->status_karyawan == 'K' ? 'Kontrak' : ($d->status_karyawan == 'T' ? 'Tetap' : ($d->status_karyawan ?? '-'));
                $encryptedNik = Crypt::encrypt($d->nik);
            @endphp
            <div class="bg-white border {{ $d->status_aktif_karyawan == '1' ? 'border-slate-200/90 hover:border-emerald-300/90' : 'border-rose-200/80 hover:border-rose-300' }} rounded-2xl p-4 sm:p-5 shadow-2xs hover:shadow-sm transition-all duration-200 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Left: Identity & Details -->
                    <div class="flex items-start sm:items-center gap-3.5 sm:gap-4 flex-1 min-w-0">
                        <!-- Row Index Badge -->
                        <div class="hidden sm:flex w-7 h-7 rounded-lg bg-slate-100 text-slate-500 text-[11px] font-bold items-center justify-center shrink-0 border border-slate-200/80">
                            {{ $loop->iteration + $karyawan->firstItem() - 1 }}
                        </div>

                        <!-- Photo Thumbnail with Status Dot -->
                        <div class="relative shrink-0">
                            @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                <img src="{{ getfotoKaryawan($d->foto) }}" alt="{{ $d->nama_karyawan }}" class="w-12 h-14 sm:w-13 sm:h-15 rounded-xl object-cover border border-slate-200 shadow-2xs">
                            @else
                                <div class="w-12 h-14 sm:w-13 sm:h-15 rounded-xl bg-slate-100 border border-slate-200/80 flex flex-col items-center justify-center text-slate-700 font-bold text-base shadow-2xs">
                                    {{ strtoupper(substr($d->nama_karyawan, 0, 1)) }}
                                </div>
                            @endif

                            @if($d->status_aktif_karyawan == '1')
                                <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] ring-2 ring-white" title="Status Aktif">
                                    <i class="ti ti-check"></i>
                                </span>
                            @else
                                <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center text-[10px] ring-2 ring-white" title="Status Nonaktif">
                                    <i class="ti ti-x"></i>
                                </span>
                            @endif
                        </div>

                        <!-- Info Content -->
                        <div class="flex-1 min-w-0">
                            <!-- Name & Status Badges -->
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('karyawan.show', $encryptedNik) }}" 
                                   class="text-sm sm:text-base font-bold text-slate-800 group-hover:text-emerald-700 transition truncate max-w-md block" title="{{ $d->nama_karyawan }}">
                                    {{ $d->nama_karyawan }}
                                </a>

                                <!-- Cabang Badge -->
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200/80">
                                    {{ $d->nama_cabang }}
                                </span>

                                <!-- Departemen Badge -->
                                @if (!empty($d->nama_dept))
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $d->nama_dept }}
                                    </span>
                                @endif

                                <!-- Status Hubungan Kerja (Tetap / Kontrak) -->
                                @if ($d->status_karyawan)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $d->status_karyawan == 'T' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $status_karyawan_text }}
                                    </span>
                                @endif

                                <!-- Upah Badge -->
                                @if ($d->jenis_upah)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        {{ $d->jenis_upah }}
                                    </span>
                                @endif

                                <!-- Status Aktif Pill -->
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold inline-flex items-center gap-1 {{ $d->status_aktif_karyawan == '1' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $d->status_aktif_karyawan == '1' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    <span>{{ $d->status_aktif_karyawan == '1' ? 'AKTIF' : 'NONAKTIF' }}</span>
                                </span>

                                <!-- User Access Badge -->
                                @if (!empty($d->id_user))
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1" title="User Login Terdaftar">
                                        <i class="ti ti-user-check"></i>
                                        <span>User Aktif</span>
                                    </span>
                                @endif
                            </div>

                            <!-- Meta Chips Flex -->
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-2 text-xs text-slate-500">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-slate-400 font-medium text-[11px]">NIK:</span>
                                    <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 text-[11px] font-mono">{{ $d->nik_show ?? $d->nik }}</span>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <span class="text-slate-400 font-medium text-[11px]">Jabatan:</span>
                                    <span class="font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 text-[11px] inline-flex items-center gap-1">
                                        <i class="ti ti-briefcase text-xs"></i>
                                        <span>{{ $d->nama_jabatan ?? '-' }}</span>
                                    </span>
                                </div>

                                <div class="flex items-center gap-1 text-[11px] text-slate-500">
                                    <i class="ti ti-calendar text-slate-400"></i>
                                    <span>Masuk: {{ date('d/m/Y', strtotime($d->tanggal_masuk)) }} ({{ $masa_kerja->y }} Th {{ $masa_kerja->m }} Bln)</span>
                                </div>

                                <!-- Security / Access Locks -->
                                <div class="flex items-center gap-2 pl-1 border-l border-slate-200">
                                    <span class="text-[11px] text-slate-400">Kontrol:</span>
                                    <!-- Location Lock -->
                                    <a href="{{ route('karyawan.lockunlocklocation', $encryptedNik) }}" 
                                       class="inline-flex items-center gap-0.5 text-[11px] font-semibold {{ $d->lock_location == '1' ? 'text-emerald-600 hover:text-emerald-700' : 'text-rose-500 hover:text-rose-600' }}"
                                       title="{{ $d->lock_location == '1' ? 'Lokasi Terkunci (Radius Kantor) - Klik Buka' : 'Bebas Lokasi - Klik Kunci' }}">
                                        <i class="ti ti-{{ $d->lock_location == '1' ? 'lock' : 'lock-open' }}"></i>
                                        <span>Lokasi</span>
                                    </a>
                                    <span class="text-slate-300">•</span>
                                    <!-- Jam Kerja Lock -->
                                    <a href="{{ route('karyawan.lockunlockjamkerja', $encryptedNik) }}" 
                                       class="inline-flex items-center gap-0.5 text-[11px] font-semibold {{ $d->lock_jam_kerja == '1' ? 'text-emerald-600 hover:text-emerald-700' : 'text-rose-500 hover:text-rose-600' }}"
                                       title="{{ $d->lock_jam_kerja == '1' ? 'Jam Kerja Terkunci - Klik Buka' : 'Bebas Jam Kerja - Klik Kunci' }}">
                                        <i class="ti ti-{{ $d->lock_jam_kerja == '1' ? 'lock' : 'lock-open' }}"></i>
                                        <span>Jam</span>
                                    </a>
                                    <span class="text-slate-300">•</span>
                                    <!-- Face Recognition Lock -->
                                    <a href="{{ route('karyawan.lockunlockfacerecognition', $encryptedNik) }}" 
                                       class="inline-flex items-center gap-0.5 text-[11px] font-semibold {{ $d->is_niqab == '0' ? 'text-emerald-600 hover:text-emerald-700' : 'text-rose-500 hover:text-rose-600' }}"
                                       title="{{ $d->is_niqab == '0' ? 'Face Recognition Terkunci - Klik Buka' : 'Face Recognition Bebas - Klik Kunci' }}">
                                        <i class="ti ti-{{ $d->is_niqab == '0' ? 'lock' : 'lock-open' }}"></i>
                                        <span>Wajah</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Action Buttons Toolbar (Icon-Only) -->
                    <div class="flex items-center justify-end gap-1 shrink-0 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                        
                        <!-- User Account Button -->
                        @can('users.create')
                            @if (empty($d->id_user))
                                <a href="{{ route('karyawan.createuser', $encryptedNik) }}" 
                                   class="w-8 h-8 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 flex items-center justify-center transition active:scale-95 shadow-2xs"
                                   title="Buat Akun User">
                                    <i class="ti ti-user-plus text-sm"></i>
                                </a>
                            @else
                                <a href="{{ route('karyawan.deleteuser', $encryptedNik) }}" 
                                   class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 hover:border-rose-200 flex items-center justify-center transition active:scale-95 shadow-2xs"
                                   title="Hapus Akun User">
                                    <i class="ti ti-user-x text-sm"></i>
                                </a>
                            @endif
                        @endcan

                        <!-- Set Jam Kerja -->
                        @can('karyawan.setjamkerja')
                            <button type="button" 
                                    class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 flex items-center justify-center transition active:scale-95 btnSetJamkerja cursor-pointer shadow-2xs"
                                    nik="{{ $encryptedNik }}"
                                    title="Set Jam Kerja">
                                <i class="ti ti-clock text-sm"></i>
                            </button>
                        @endcan

                        <!-- Set Cabang -->
                        @can('karyawan.setcabang')
                            <button type="button" 
                                    class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 flex items-center justify-center transition active:scale-95 btnSetCabang cursor-pointer shadow-2xs"
                                    nik="{{ $encryptedNik }}"
                                    title="Set Cabang">
                                <i class="ti ti-map-pin text-sm"></i>
                            </button>
                        @endcan

                        <!-- Edit -->
                        @can('karyawan.edit')
                            <button type="button" 
                                    class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 border border-blue-200 flex items-center justify-center transition active:scale-95 btnEdit cursor-pointer shadow-2xs"
                                    nik="{{ $encryptedNik }}"
                                    title="Edit Karyawan">
                                <i class="ti ti-edit text-sm"></i>
                            </button>
                        @endcan

                        <!-- Detail -->
                        @can('karyawan.show')
                            <a href="{{ route('karyawan.show', $encryptedNik) }}" 
                               class="w-8 h-8 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 flex items-center justify-center transition active:scale-95 shadow-2xs"
                               title="Detail Karyawan">
                                <i class="ti ti-file-description text-sm"></i>
                            </a>
                        @endcan

                        <!-- Delete -->
                        @can('karyawan.delete')
                            <form method="POST" action="{{ route('karyawan.delete', $encryptedNik) }}" class="deleteform inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 delete-confirm cursor-pointer shadow-2xs" 
                                        title="Hapus Karyawan">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            </form>
                        @endcan

                    </div>

                </div>
            </div>
        @empty
            <div class="py-12 text-center bg-white rounded-2xl border border-slate-200 shadow-2xs">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                        <i class="ti ti-users text-xl"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">Tidak ada data karyawan ditemukan</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Coba gunakan filter lain atau tambahkan karyawan baru.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- ================= 5. EMPLOYEE LIST: TABLE VIEW (SEAMLESS SOLID HEADER ALA SIPRENPAS) ================= -->
    <div x-show="viewMode === 'table'" class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        
        <!-- Seamless Solid Card Header -->
        <div class="px-5 pt-4 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs">
                    <i class="ti ti-id-badge-2"></i>
                </div>
                <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Data Karyawan</h3>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                    {{ $karyawan->total() }} data
                </span>
            </div>
            <div class="text-xs text-white/85 font-medium">
                Kelola profil pegawai, penempatan unit kerja, jadwal presensi, dan kontrol akses
            </div>
        </div>

        <!-- Responsive Table Container -->
        <div class="overflow-x-auto" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                <!-- Matching Solid Table Header -->
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 w-12 text-center text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Karyawan</th>
                        <th class="py-2.5 px-3 text-white/90 border-0 border-t-0">Jabatan & Dept</th>
                        <th class="py-2.5 px-3 text-white/90 border-0 border-t-0">Cabang & Status</th>
                        <th class="py-2.5 px-3 text-center text-white/90 border-0 border-t-0">Masa Kerja</th>
                        <th class="py-2.5 px-3 text-center text-white/90 border-0 border-t-0">Status</th>
                        <th class="py-2.5 px-3 text-center text-white/90 border-0 border-t-0">Kontrol Akses</th>
                        <th class="py-2.5 px-4 text-end text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($karyawan as $d)
                        @php
                            $awal = new DateTime($d->tanggal_masuk);
                            $akhir = new DateTime();
                            $masa_kerja = $akhir->diff($awal);
                            $status_karyawan_text = $d->status_karyawan == 'K' ? 'Kontrak' : ($d->status_karyawan == 'T' ? 'Tetap' : ($d->status_karyawan ?? '-'));
                            $encryptedNik = Crypt::encrypt($d->nik);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            
                            <!-- No Index -->
                            <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold text-xs">
                                <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                    {{ $loop->iteration + $karyawan->firstItem() - 1 }}
                                </span>
                            </td>

                            <!-- Identity -->
                            <td class="py-2.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0 flex items-center justify-center font-bold text-xs text-slate-600">
                                        @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                            <img src="{{ getfotoKaryawan($d->foto) }}" alt="{{ $d->nama_karyawan }}" class="w-full h-full object-cover">
                                        @else
                                            <span>{{ substr($d->nama_karyawan, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('karyawan.show', $encryptedNik) }}" 
                                           class="font-semibold text-slate-800 hover:text-emerald-700 transition block truncate">
                                            {{ $d->nama_karyawan }}
                                        </a>
                                        <span class="text-[11px] text-slate-400 font-mono">
                                            {{ $d->nik_show ?? $d->nik }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Jabatan & Departemen -->
                            <td class="py-2.5 px-3">
                                <div class="font-medium text-slate-800">{{ $d->nama_jabatan }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $d->nama_dept }}</div>
                            </td>

                            <!-- Cabang & Status Hubungan Kerja -->
                            <td class="py-2.5 px-3">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $d->nama_cabang }}
                                    </span>
                                    @if ($d->status_karyawan)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10.5px] font-medium {{ $d->status_karyawan == 'T' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $status_karyawan_text }}
                                        </span>
                                    @endif
                                    @if ($d->jenis_upah)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                            {{ $d->jenis_upah }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Masa Kerja & Masuk -->
                            <td class="py-2.5 px-3 text-center">
                                <div class="font-semibold text-slate-700 text-[11.5px]">
                                    {{ $masa_kerja->y . ' Th ' . $masa_kerja->m . ' Bln' }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Masuk: {{ date('d/m/Y', strtotime($d->tanggal_masuk)) }}
                                </div>
                            </td>

                            <!-- Status Aktif Badge -->
                            <td class="py-2.5 px-3 text-center">
                                @if ($d->status_aktif_karyawan == '1')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Non-Aktif</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Kontrol Akses (Security Toggles) -->
                            <td class="py-2.5 px-3 text-center">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <!-- Lock Location -->
                                    <a href="{{ route('karyawan.lockunlocklocation', $encryptedNik) }}" 
                                       class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ $d->lock_location == '1' ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-500 hover:bg-rose-100' }}"
                                       title="{{ $d->lock_location == '1' ? 'Lokasi Terkunci (Radius Kantor) - Klik Buka' : 'Bebas Lokasi - Klik Kunci' }}">
                                        <i class="ti ti-{{ $d->lock_location == '1' ? 'lock' : 'lock-open' }}"></i>
                                    </a>

                                    <!-- Lock Jam Kerja -->
                                    <a href="{{ route('karyawan.lockunlockjamkerja', $encryptedNik) }}" 
                                       class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ $d->lock_jam_kerja == '1' ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-500 hover:bg-rose-100' }}"
                                       title="{{ $d->lock_jam_kerja == '1' ? 'Jam Kerja Terkunci - Klik Buka' : 'Bebas Jam Kerja - Klik Kunci' }}">
                                        <i class="ti ti-{{ $d->lock_jam_kerja == '1' ? 'lock' : 'lock-open' }}"></i>
                                    </a>

                                    <!-- Lock Face Recognition -->
                                    <a href="{{ route('karyawan.lockunlockfacerecognition', $encryptedNik) }}" 
                                       class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ $d->is_niqab == '0' ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-500 hover:bg-rose-100' }}"
                                       title="{{ $d->is_niqab == '0' ? 'Face Recognition Terkunci - Klik Buka' : 'Face Recognition Bebas - Klik Kunci' }}">
                                        <i class="ti ti-{{ $d->is_niqab == '0' ? 'face-id' : 'lock-open' }}"></i>
                                    </a>
                                </div>
                            </td>

                            <!-- Actions Bar -->
                            <td class="py-2.5 px-4 text-end">
                                <div class="inline-flex items-center gap-1 justify-end">
                                    <!-- User Account Pill Button (Icon-Only) -->
                                    @can('users.create')
                                        @if (empty($d->id_user))
                                            <a href="{{ route('karyawan.createuser', $encryptedNik) }}" 
                                               class="w-8 h-8 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 flex items-center justify-center transition active:scale-95 shadow-2xs"
                                               title="Buat Akun User">
                                                <i class="ti ti-user-plus text-sm"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('karyawan.deleteuser', $encryptedNik) }}" 
                                               class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 hover:border-rose-200 flex items-center justify-center transition active:scale-95 shadow-2xs"
                                               title="Hapus Akun User">
                                                <i class="ti ti-user-x text-sm"></i>
                                            </a>
                                        @endif
                                    @endcan

                                    <!-- Set Jam Kerja -->
                                    @can('karyawan.setjamkerja')
                                        <button type="button" 
                                                class="btnSetJamkerja w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer"
                                                nik="{{ $encryptedNik }}" 
                                                title="Set Jam Kerja">
                                            <i class="ti ti-clock text-sm"></i>
                                        </button>
                                    @endcan

                                    <!-- Set Cabang -->
                                    @can('karyawan.setcabang')
                                        <button type="button" 
                                                class="btnSetCabang w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer"
                                                nik="{{ $encryptedNik }}" 
                                                title="Set Cabang">
                                            <i class="ti ti-map-pin text-sm"></i>
                                        </button>
                                    @endcan

                                    <!-- Edit -->
                                    @can('karyawan.edit')
                                        <button type="button" 
                                                class="btnEdit w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 border border-blue-200 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer"
                                                nik="{{ $encryptedNik }}" 
                                                title="Edit Karyawan">
                                            <i class="ti ti-edit text-sm"></i>
                                        </button>
                                    @endcan

                                    <!-- Show Detail -->
                                    @can('karyawan.show')
                                        <a href="{{ route('karyawan.show', $encryptedNik) }}" 
                                           class="w-8 h-8 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 flex items-center justify-center transition active:scale-95 shadow-2xs"
                                           title="Detail Profil">
                                            <i class="ti ti-file-description text-sm"></i>
                                        </a>
                                    @endcan

                                    <!-- Delete -->
                                    @can('karyawan.delete')
                                        <form method="POST" action="{{ route('karyawan.delete', $encryptedNik) }}" class="deleteform inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="delete-confirm w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer"
                                                    title="Hapus Karyawan">
                                                <i class="ti ti-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                Tidak ada data karyawan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= 6. PAGINATION ================= -->
    <div class="pt-1">
        {{ $karyawan->links('v2.components.pagination') }}
    </div>

</div>

<!-- ================= MODAL CONTAINERS (Bootstrap 5 Standard identical to siprenpas) ================= -->

<!-- Modal 1: Form Create / Edit Karyawan -->
<div class="modal fade" id="modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border border-slate-200/90 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-200/90 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center text-base shrink-0 font-bold">
                        <i class="ti ti-user-check"></i>
                    </div>
                    <h5 class="modal-title text-base font-bold text-slate-900 tracking-tight truncate mb-0" id="modalTitle">Form Data Karyawan</h5>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="modal-body p-6 bg-white" id="loadmodal">
                <!-- Dynamic content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Set Jam Kerja -->
<div class="modal fade" id="modalSetJamkerja" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border border-slate-200/90 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-200/90 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center text-base shrink-0 font-bold">
                        <i class="ti ti-clock"></i>
                    </div>
                    <h5 class="modal-title text-base font-bold text-slate-900 tracking-tight truncate mb-0" id="modalSetJamkerjaTitle">Set Jam Kerja Karyawan</h5>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="modal-body p-6 bg-white" id="loadmodalSetJamkerja">
                <!-- Dynamic content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Set Cabang -->
<div class="modal fade" id="modalSetCabang" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border border-slate-200/90 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-200/90 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center text-base shrink-0 font-bold">
                        <i class="ti ti-building"></i>
                    </div>
                    <h5 class="modal-title text-base font-bold text-slate-900 tracking-tight truncate mb-0" id="modalSetCabangTitle">Set Cabang Karyawan</h5>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="modal-body p-6 bg-white" id="loadmodalSetCabang">
                <!-- Dynamic content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal 4: Import Excel -->
<div class="modal fade" id="modalImport" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border border-slate-200/90 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
                        <i class="ti ti-file-spreadsheet"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalImportTitle">Import Data Karyawan</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium">Unggah berkas excel untuk memasukkan data secara massal</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="modal-body p-6 bg-white" id="loadmodalImport">
                <!-- Dynamic content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    // Universal modal helper functions
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Modal spinner loader
    function showLoadingSpinner(targetId) {
        const spinner = `
            <div class="flex flex-col items-center justify-center py-12 gap-3 text-slate-400">
                <div class="w-8 h-8 rounded-full border-2 border-slate-200 border-t-emerald-600 animate-spin"></div>
                <span class="text-xs font-medium">Memuat formulir...</span>
            </div>
        `;
        $(`#${targetId}`).html(spinner);
    }

    $(function() {
        // Universal open modal helper
        window.openModal = function(modalId) {
            const el = document.getElementById(modalId);
            if (el) {
                const modal = bootstrap.Modal.getOrCreateInstance(el, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
            }
        };

        window.closeModal = function(modalId) {
            const el = document.getElementById(modalId);
            if (el) {
                const modal = bootstrap.Modal.getInstance(el);
                if (modal) modal.hide();
            }
        };

        // 1. Tambah Karyawan
        $("#btnCreate").click(function(e) {
            e.preventDefault();
            $("#modalTitle").text("Tambah Data Karyawan");
            openModal("modal");
            showLoadingSpinner("loadmodal");
            $("#loadmodal").load("{{ route('karyawan.create') }}");
        });

        // 2. Import Excel
        $("#btnImport").click(function(e) {
            e.preventDefault();
            $("#modalImportTitle").text("Import Data Karyawan");
            openModal("modalImport");
            showLoadingSpinner("loadmodalImport");
            $("#loadmodalImport").load("{{ route('karyawan.import') }}");
        });

        // 3. Edit Karyawan
        $(document).on("click", ".btnEdit", function(e) {
            e.preventDefault();
            const nik = $(this).attr("nik");
            $("#modalTitle").text("Edit Data Karyawan");
            openModal("modal");
            showLoadingSpinner("loadmodal");
            $("#loadmodal").load(`/karyawan/${nik}/edit`);
        });

        // 4. Set Jam Kerja
        $(document).on("click", ".btnSetJamkerja", function(e) {
            e.preventDefault();
            const nik = $(this).attr("nik");
            $("#modalSetJamkerjaTitle").text("Set Jam Kerja Karyawan");
            openModal("modalSetJamkerja");
            showLoadingSpinner("loadmodalSetJamkerja");
            $("#loadmodalSetJamkerja").load(`/karyawan/${nik}/setjamkerja`);
        });

        // 5. Set Cabang
        $(document).on("click", ".btnSetCabang", function(e) {
            e.preventDefault();
            const nik = $(this).attr("nik");
            $("#modalSetCabangTitle").text("Set Cabang Karyawan");
            openModal("modalSetCabang");
            showLoadingSpinner("loadmodalSetCabang");
            $("#loadmodalSetCabang").load(`/karyawan/${nik}/setcabang`);
        });

        // 6. Delete Confirm (SweetAlert2)
        $(document).on("click", ".delete-confirm", function(e) {
            e.preventDefault();
            const form = $(this).closest("form");
            Swal.fire({
                title: "Hapus Karyawan?",
                text: "Data karyawan yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e11d48",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Ya, Hapus Data",
                cancelButtonText: "Batal",
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl text-xs font-bold px-4 py-2',
                    cancelButton: 'rounded-xl text-xs font-bold px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // 7. Reset All User Confirm
        $(document).on("click", ".delete-all-user", function(e) {
            e.preventDefault();
            const href = $(this).attr("href");
            Swal.fire({
                title: "Reset Semua User?",
                text: "Semua akun user dengan role Karyawan akan dihapus dari sistem!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e11d48",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Ya, Reset Semua",
                cancelButtonText: "Batal",
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl text-xs font-bold px-4 py-2',
                    cancelButton: 'rounded-xl text-xs font-bold px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });

        // Initialize tooltips
        if (window.initTooltips) {
            window.initTooltips();
        }
    });
</script>
@endpush
