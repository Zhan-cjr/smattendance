@extends('v2.layouts.app')

@section('titlepage', 'Manajemen Kontrak')
@section('page_title', 'Manajemen Kontrak')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $user = auth()->user();
@endphp

<div class="space-y-4" x-data="{ viewMode: 'table' }">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-file-certificate text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Manajemen Kontrak Kerja</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola status perjanjian kerja (PKWT/PKWTT), masa berlaku kontrak, dan riwayat kompensasi karyawan
            </p>
        </div>

        <!-- Breadcrumbs & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500">Karyawan</span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Kontrak</span>
            </nav>

            <div class="flex flex-wrap items-center gap-2">
                @can('kontrak.create')
                    <a href="{{ route('kontrak.template') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-slate-900 text-xs font-bold transition shadow-2xs cursor-pointer">
                        <i class="ti ti-settings text-sm text-sky-600"></i>
                        <span>Template Kontrak</span>
                    </a>

                    <button type="button" id="btnCreateKontrakV2"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Kontrak</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (Session::get('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-circle-check text-emerald-600 text-base"></i>
                <span class="font-semibold">{{ Session::get('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900"><i class="ti ti-x"></i></button>
        </div>
    @endif
    @if (Session::get('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-900 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-alert-circle text-rose-600 text-base"></i>
                <span class="font-semibold">{{ Session::get('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900"><i class="ti ti-x"></i></button>
        </div>
    @endif

    <!-- ================= 2. EXECUTIVE STATISTICS CARD (SOLID THEME SEAMLESS WITH TAPERED DIVIDERS) ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        
        <!-- Clean Minimalist Corner Watermark & Glow -->
        <div class="absolute -right-6 -bottom-6 w-56 h-56 rounded-full bg-white/5 pointer-events-none blur-xl"></div>
        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-white/[0.07] pointer-events-none select-none">
            <i class="ti ti-file-certificate text-[180px] leading-none"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            
            <!-- Segment 1: Total Kontrak -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Kontrak
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25 shadow-2xs">
                            Semua
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($stats['total'] ?? 0) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-folders text-xs opacity-80"></i>
                        <span>Arsip Kontrak</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        100%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Kontrak Aktif -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Kontrak Aktif
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-400/40 text-white border border-emerald-300/50 shadow-2xs">
                            Aktif
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($stats['aktif'] ?? 0) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-user-check text-xs opacity-80"></i>
                        <span>Berlaku Saat Ini</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ ($stats['total'] ?? 0) > 0 ? round((($stats['aktif'] ?? 0) / $stats['total']) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: PKWT (Kontrak) -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Pegawai PKWT
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-sky-400/30 text-white border border-sky-300/40 shadow-2xs">
                            PKWT
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($stats['pkwt'] ?? 0) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-calendar-time text-xs opacity-80"></i>
                        <span>Waktu Tertentu</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ ($stats['total'] ?? 0) > 0 ? round((($stats['pkwt'] ?? 0) / $stats['total']) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 4: PKWTT (Tetap) -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Pegawai Tetap
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400/30 text-white border border-amber-300/40 shadow-2xs">
                            PKWTT
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($stats['pkwtt'] ?? 0) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-shield-check text-xs opacity-80"></i>
                        <span>Waktu Tdk Tertentu</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ ($stats['total'] ?? 0) > 0 ? round((($stats['pkwtt'] ?? 0) / $stats['total']) * 100) : 0 }}%
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= 3. FILTER & SEARCH TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('kontrak.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                
                <!-- Search Nama / No Dokumen -->
                <div class="lg:col-span-4 relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" name="nama_karyawan" value="{{ request('nama_karyawan') }}"
                           placeholder="Cari nama karyawan, no. kontrak, no. dokumen..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium"
                           style="--tw-ring-color: {{ $themeColor1 }};">
                </div>

                <!-- Cabang Filter -->
                <div class="lg:col-span-3">
                    <select name="kode_cabang"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium cursor-pointer"
                            style="--tw-ring-color: {{ $themeColor1 }};">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabangs as $cabang)
                            <option value="{{ $cabang->kode_cabang }}" {{ request('kode_cabang') == $cabang->kode_cabang ? 'selected' : '' }}>
                                {{ $cabang->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Departemen Filter -->
                <div class="lg:col-span-3">
                    <select name="kode_dept"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium cursor-pointer"
                            style="--tw-ring-color: {{ $themeColor1 }};">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemens as $dept)
                            <option value="{{ $dept->kode_dept }}" {{ request('kode_dept') == $dept->kode_dept ? 'selected' : '' }}>
                                {{ $dept->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="lg:col-span-2 flex gap-1.5">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center p-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});"
                            title="Terapkan Filter">
                        <i class="ti ti-filter text-base me-1"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['nama_karyawan', 'kode_cabang', 'kode_dept']))
                        <a href="{{ route('kontrak.index') }}"
                           class="inline-flex items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800 text-xs font-bold transition shadow-2xs cursor-pointer"
                           title="Reset Filter">
                            <i class="ti ti-refresh text-base"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <!-- Counter Sub-header -->
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <div class="text-[11.5px]">
                Menampilkan <strong>{{ $kontraks->firstItem() ?? 0 }}</strong> - <strong>{{ $kontraks->lastItem() ?? 0 }}</strong> dari <strong>{{ $kontraks->total() }}</strong> berkas kontrak
            </div>
            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                {{ $kontraks->total() }} Berkas
            </span>
        </div>
    </div>

    <!-- ================= 4. KONTRAK CARD LIST (FULL WIDTH RESPONSIVE) ================= -->
    <div class="space-y-3">
        @forelse ($kontraks as $kontrak)
            @php
                $isPKWTT = $kontrak->jenis_kontrak === 'T';
                $jenisLabel = $isPKWTT ? 'Tetap (PKWTT)' : 'Kontrak (PKWT)';
                $isActive = $kontrak->status_kontrak == '1';
                $initial = substr($kontrak->nama_karyawan ?? 'K', 0, 1);
                $hasPhoto = !empty($kontrak->foto) && Storage::disk('public')->exists('/karyawan/' . $kontrak->foto);
            @endphp

            <div class="rounded-2xl border transition-all duration-200 p-4 sm:p-5 group {{ $isActive ? 'bg-white border-slate-200/90 hover:border-slate-300 hover:shadow-sm' : 'bg-rose-50/50 border-rose-200/90 hover:bg-rose-50/70 hover:border-rose-300' }}">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Left: Foto Karyawan + Identitas + Status Tag -->
                    <div class="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
                        <!-- Foto Avatar -->
                        <div class="w-12 h-12 rounded-2xl overflow-hidden shrink-0 border shadow-2xs flex items-center justify-center font-black text-base {{ $isActive ? 'border-slate-200/90 text-white' : 'border-rose-200 text-rose-500 bg-rose-100/70' }}"
                             @if(!$hasPhoto && $isActive) style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});" @endif>
                            @if ($hasPhoto)
                                <img src="{{ getfotoKaryawan($kontrak->foto) }}" alt="{{ $kontrak->nama_karyawan }}" class="w-full h-full object-cover {{ !$isActive ? 'grayscale-50' : '' }}">
                            @else
                                <span>{{ $initial }}</span>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm sm:text-base font-bold {{ $isActive ? 'text-slate-900 group-hover:text-emerald-700' : 'text-slate-800' }} transition truncate">
                                    {{ $kontrak->nama_karyawan ?? '-' }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold {{ $isActive ? 'bg-slate-100 text-slate-600 border border-slate-200/80' : 'bg-white/80 text-rose-800 border border-rose-200' }}">
                                    NIK: {{ $kontrak->nik_show ?? $kontrak->nik }}
                                </span>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold {{ $isActive ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-white/80 text-rose-900 border border-rose-200' }}">
                                    <i class="ti ti-file-certificate text-[11px] {{ $isActive ? 'text-slate-500' : 'text-rose-500' }}"></i>
                                    {{ $kontrak->no_dokumen ?? $kontrak->no_kontrak }}
                                </span>
                                @if(!$isActive)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200 flex items-center gap-1">
                                        <i class="ti ti-history text-[11px]"></i> Riwayat / Digantikan
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs {{ $isActive ? 'text-slate-500' : 'text-slate-600' }} font-medium">
                                <span class="font-bold {{ $isActive ? 'text-slate-700' : 'text-slate-800' }} flex items-center gap-1">
                                    <i class="ti ti-briefcase text-slate-400"></i>
                                    {{ $kontrak->nama_jabatan ?? 'Staf' }}
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-building text-slate-400"></i>
                                    {{ $kontrak->nama_dept ?? '-' }}
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-map-pin text-slate-400"></i>
                                    {{ $kontrak->nama_cabang ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Center: Periode Masa Kontrak & Status PKWT/PKWTT -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex items-center gap-4 sm:gap-6 py-3 lg:py-0 border-y lg:border-y-0 lg:border-x {{ $isActive ? 'border-slate-200/80' : 'border-rose-200/80' }} lg:px-6">
                        <!-- Masa Kontrak -->
                        <div class="min-w-[150px]">
                            <span class="block text-[10px] font-bold {{ $isActive ? 'text-slate-400' : 'text-rose-500/80' }} uppercase tracking-wider mb-1">Masa Kontrak</span>
                            @if ($isPKWTT)
                                <div class="text-xs font-bold text-amber-700 flex items-center gap-1">
                                    <i class="ti ti-infinity"></i>
                                    <span>Pegawai Tetap</span>
                                </div>
                            @elseif($kontrak->dari && $kontrak->sampai)
                                <div class="text-xs font-bold {{ $isActive ? 'text-slate-800' : 'text-slate-700' }}">
                                    {{ date('d M Y', strtotime($kontrak->dari)) }} - {{ date('d M Y', strtotime($kontrak->sampai)) }}
                                </div>
                                <div class="text-[10.5px] {{ $isActive ? 'text-slate-400' : 'text-rose-600/80' }} mt-0.5">
                                    Durasi: {{ \Carbon\Carbon::parse($kontrak->dari)->diffInMonths(\Carbon\Carbon::parse($kontrak->sampai)) }} Bulan
                                </div>
                            @else
                                <div class="text-xs font-medium text-slate-400">-</div>
                            @endif
                        </div>

                        <!-- Status & Tipe -->
                        <div class="min-w-[130px]">
                            <span class="block text-[10px] font-bold {{ $isActive ? 'text-slate-400' : 'text-rose-500/80' }} uppercase tracking-wider mb-1">Tipe & Status</span>
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold {{ $isPKWTT ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-sky-50 text-sky-700 border border-sky-200' }}">
                                    {{ $jenisLabel }}
                                </span>
                                @if ($isActive)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200 shadow-2xs" title="Kontrak ini telah kadaluarsa atau digantikan oleh kontrak baru">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span> Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right: Action Buttons -->
                    <div class="flex items-center justify-end gap-1.5 shrink-0">
                        <!-- Print -->
                        <a href="{{ route('kontrak.print', Crypt::encrypt($kontrak->id)) }}" target="_blank"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition shadow-2xs"
                           title="Cetak Kontrak">
                            <i class="ti ti-printer text-sm text-slate-600"></i>
                            <span class="hidden sm:inline">Cetak</span>
                        </a>

                        <!-- Download PDF Lampiran -->
                        @if ($kontrak->dokumen)
                            <a href="{{ asset('storage/uploads/kontrak/' . $kontrak->dokumen) }}" target="_blank"
                               class="w-8 h-8 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-600 border border-sky-200 flex items-center justify-center transition active:scale-95 shadow-2xs"
                               title="Unduh Lampiran PDF">
                                <i class="ti ti-download text-xs"></i>
                            </a>
                        @endif

                        @if ($isActive)
                            @can('kontrak.edit')
                                <button type="button" 
                                        class="btnEditKontrakV2 w-8 h-8 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer"
                                        data-href="{{ route('kontrak.edit', Crypt::encrypt($kontrak->id)) }}"
                                        title="Edit Kontrak">
                                    <i class="ti ti-edit text-xs"></i>
                                </button>
                            @endcan

                            @can('kontrak.delete')
                                <form action="{{ route('kontrak.delete', Crypt::encrypt($kontrak->id)) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btnDeleteKontrakV2 w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer" title="Hapus Kontrak">
                                        <i class="ti ti-trash text-xs"></i>
                                    </button>
                                </form>
                            @endcan
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="py-12 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                <i class="ti ti-file-off text-4xl block mb-2 text-slate-300"></i>
                <h4 class="text-sm font-bold text-slate-700">Tidak ada berkas kontrak ditemukan</h4>
                <p class="text-xs text-slate-400 mt-1">Coba gunakan filter pencarian lain atau tambahkan kontrak kerja baru.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($kontraks->hasPages())
        <div class="pt-2">
            {{ $kontraks->links('v2.components.pagination') }}
        </div>
    @endif

</div>

<!-- ================= 6. DYNAMIC AJAX MODAL KONTRAK ================= -->
<div id="modalKontrakV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all my-6">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}12);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-file-certificate text-lg" id="modalKontrakIcon"></i>
                </div>
                <div>
                    <h2 class="text-sm sm:text-base font-black text-slate-800 tracking-tight" id="modalKontrakTitle">Kontrak Kerja</h2>
                    <p class="text-[11px] text-slate-500 font-medium" id="modalKontrakSubtitle">Formulir data perjanjian kerja karyawan</p>
                </div>
            </div>
            <button type="button" class="btnCloseModalKontrak w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 flex items-center justify-center transition cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <div class="p-6 max-h-[82vh] overflow-y-auto" id="modalKontrakBody">
            <div class="py-12 text-center text-slate-400">
                <svg class="animate-spin h-8 w-8 text-emerald-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-semibold">Memuat Form Kontrak...</span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {
    function openModal() {
        $('#modalKontrakV2').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    }

    function closeModal() {
        $('#modalKontrakV2').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    }

    $(document).on('click', '.btnCloseModalKontrak', closeModal);

    $('#modalKontrakV2').on('click', function(e) {
        if (e.target === this) closeModal();
    });

    function showLoading() {
        $('#modalKontrakBody').html(`
            <div class="py-12 text-center text-slate-400">
                <svg class="animate-spin h-8 w-8 text-emerald-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-semibold">Memuat Form Kontrak...</span>
            </div>
        `);
    }

    // Create Modal
    $('#btnCreateKontrakV2').on('click', function() {
        $('#modalKontrakTitle').text('Tambah Kontrak Kerja Baru');
        $('#modalKontrakSubtitle').text('Isi rincian masa kontrak, status PKWT/PKWTT, dan kompensasi');
        $('#modalKontrakIcon').attr('class', 'ti ti-plus text-base');
        showLoading();
        openModal();
        $('#modalKontrakBody').load("{{ route('kontrak.create') }}");
    });

    // Edit Modal
    $(document).on('click', '.btnEditKontrakV2', function() {
        const href = $(this).data('href');
        $('#modalKontrakTitle').text('Edit Kontrak Kerja');
        $('#modalKontrakSubtitle').text('Perbarui nomor dokumen, masa berlaku, atau rincian gaji');
        $('#modalKontrakIcon').attr('class', 'ti ti-edit text-base');
        showLoading();
        openModal();
        $('#modalKontrakBody').load(href);
    });

    // Delete Confirmation
    $(document).on('click', '.btnDeleteKontrakV2', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Berkas Kontrak Ini?',
            text: 'Data kontrak kerja yang dihapus tidak dapat dipulihkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl shadow-xl border border-slate-100',
                confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold',
                cancelButton: 'px-4 py-2 rounded-xl text-xs font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
