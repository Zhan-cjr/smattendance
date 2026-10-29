@extends('v2.layouts.app')

@section('titlepage', 'Setting Karyawan - ' . $periode_gaji->nama_periode)
@section('page_title', 'Setting Karyawan Periode Gaji')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalKaryawanAktif = count($karyawan_periode);

    $metodeText = 'Satu Bulan Sama';
    if ($periode_gaji->metode_lintas_bulan == 1) {
        $metodeText = 'Bulan Lalu ke Bulan Ini';
    } elseif ($periode_gaji->metode_lintas_bulan == 2) {
        $metodeText = 'Bulan Ini ke Depan';
    }
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle with Back Button -->
        <div class="flex items-center gap-3">
            <a href="{{ route('periodegaji.index') }}" 
               class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-2xs cursor-pointer shrink-0">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('periodegaji.index') }}" class="hover:text-emerald-600 transition-colors">Periode Gaji</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Setting Karyawan</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2 mt-0.5">
                    <span>Setting Karyawan:</span>
                    <span class="text-emerald-700 font-extrabold">{{ $periode_gaji->nama_periode }}</span>
                    <span class="font-mono text-xs font-bold px-2 py-0.5 bg-slate-100 border border-slate-200 text-slate-700 rounded-md">
                        {{ $periode_gaji->kode_periode_gaji }}
                    </span>
                </h1>
            </div>
        </div>

        <!-- Right Side: Actions Buttons -->
        <div class="flex items-center gap-2">
            @can('periodegaji.setting')
                <button type="button" id="btnOpenModalAdd" 
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-user-plus text-sm"></i>
                    <span>Tambah Karyawan (Massal)</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- ================= 2. EXECUTIVE METRICS CARD ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-users-group text-[190px]"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            <!-- Segment 1: Periode & Kode -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Periode Siklus
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            {{ $periode_gaji->kode_periode_gaji }}
                        </span>
                    </div>
                    <div class="text-xl sm:text-2xl font-black text-white tracking-tight mt-1.5 truncate">
                        {{ $periode_gaji->nama_periode }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-id text-xs opacity-80"></i>
                        <span>Kode Sistem</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md font-mono">
                        {{ $periode_gaji->kode_periode_gaji }}
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Rentang Cut-Off & Siklus -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Rentang Cut-Off
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Absen
                        </span>
                    </div>
                    <div class="text-xl sm:text-2xl font-black text-white tracking-tight mt-1.5">
                        Tgl {{ $periode_gaji->tanggal_dari }} s/d {{ $periode_gaji->tanggal_sampai }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-arrows-left-right text-xs opacity-80"></i>
                        <span>Metode Siklus</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md truncate max-w-[130px]">
                        {{ $metodeText }}
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Total Karyawan Terdaftar -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Karyawan Terdaftar
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Aktif
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalKaryawanAktif) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-user-check text-xs opacity-80"></i>
                        <span>Total Pegawai</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        100%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. FILTER TOOLBAR STANDAR V2 ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('periodegaji.setting', $periode_gaji->kode_periode_gaji) }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-2.5 sm:gap-3 items-center">
                
                <!-- Cari Nama Karyawan -->
                <div class="md:col-span-5 relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-search text-sm"></i>
                    </span>
                    <input type="text" name="nama_karyawan" id="filter_nama_karyawan" 
                           value="{{ request('nama_karyawan') }}" 
                           placeholder="Cari Nama atau NIK Karyawan..." 
                           class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 placeholder-slate-400 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                </div>

                <!-- Filter Cabang -->
                <div class="md:col-span-3">
                    <select name="kode_cabang" id="filter_kode_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Departemen -->
                <div class="md:col-span-2">
                    <select name="kode_dept" id="filter_kode_dept" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions Button (Filter & Reset) -->
                <div class="md:col-span-2 flex items-center gap-1.5">
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if (request()->hasAny(['nama_karyawan', 'kode_cabang', 'kode_dept']))
                        <a href="{{ route('periodegaji.setting', $periode_gaji->kode_periode_gaji) }}" 
                           class="inline-flex items-center justify-center p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs cursor-pointer"
                           title="Reset Filter">
                            <i class="ti ti-refresh text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <!-- Divider with Counter -->
        <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="text-slate-500 text-[11.5px] flex items-center gap-2">
                <span>Menampilkan <strong>{{ count($karyawan_periode) }}</strong> Karyawan</span>
            </div>
        </div>
    </div>

    <!-- ================= 4. LIST CARD KARYAWAN TERDAFTAR ================= -->
    <div class="space-y-3">
        @forelse ($karyawan_periode as $item)
            @php
                $k = $item->karyawan;
                $foto = $k->foto ?? null;
                $fotoUrl = (!empty($foto) && Storage::disk('public')->exists('/karyawan/' . $foto))
                    ? getfotoKaryawan($foto)
                    : asset('assets/img/avatars/No_Image_Available.jpg');
                
                $bulanTeks = !empty($item->bulan) ? getNamabulan($item->bulan) : (!empty($item->created_at) ? getNamabulan(date('n', strtotime($item->created_at))) : date('F'));
                $tahunTeks = !empty($item->tahun) ? $item->tahun : (!empty($item->created_at) ? date('Y', strtotime($item->created_at)) : date('Y'));
                
                $statusText = ($k && $k->status_karyawan == 'K') ? 'Kontrak' : (($k && $k->status_karyawan == 'T') ? 'Tetap' : ($k->status_karyawan ?? ''));
                $statusBadge = ($k && $k->status_karyawan == 'T') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-sky-50 text-sky-700 border-sky-200';
            @endphp
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-slate-300 transition-all duration-200 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Left: Identity (Avatar, Nama, NIK, Status) -->
                    <div class="flex items-center gap-3.5 min-w-0 lg:w-4/12">
                        <div class="relative shrink-0">
                            <img src="{{ $fotoUrl }}" alt="Foto" 
                                 class="w-12 h-12 rounded-xl object-cover border border-slate-200/80 shadow-2xs">
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs sm:text-sm font-black text-slate-900 truncate group-hover:text-emerald-700 transition-colors">
                                {{ $k->nama_karyawan ?? '-' }}
                            </h4>
                            <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                <span class="font-mono text-[10.5px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $item->nik }}
                                </span>
                                @if ($statusText)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $statusBadge }}">
                                        {{ $statusText }}
                                    </span>
                                @endif
                                @if ($k && !empty($k->jenis_upah))
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $k->jenis_upah }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Middle: Placement (Cabang, Departemen, Jabatan) -->
                    <div class="grid grid-cols-2 sm:grid-cols-2 gap-3 lg:w-4/12 pt-2 sm:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-4">
                        <div>
                            <span class="text-[10.5px] font-semibold text-slate-400 block uppercase tracking-wider">Cabang & Dept</span>
                            <span class="text-xs font-extrabold text-slate-800 block truncate mt-0.5">
                                {{ $k->cabang->nama_cabang ?? ($k->kode_cabang ?? '-') }}
                            </span>
                            <span class="text-[11px] text-slate-500 flex items-center gap-1 truncate mt-0.5">
                                <i class="ti ti-building text-xs text-slate-400"></i>
                                {{ $k->departemen->nama_dept ?? ($k->kode_dept ?? '-') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10.5px] font-semibold text-slate-400 block uppercase tracking-wider">Jabatan</span>
                            <span class="text-xs font-extrabold text-slate-800 block truncate mt-0.5">
                                {{ $k->jabatan->nama_jabatan ?? '-' }}
                            </span>
                            <span class="text-[11px] text-slate-500 flex items-center gap-1 truncate mt-0.5">
                                <i class="ti ti-briefcase text-xs text-slate-400"></i>
                                Penempatan Aktif
                            </span>
                        </div>
                    </div>

                    <!-- Berlaku Mulai & Actions -->
                    <div class="flex items-center justify-between lg:justify-end gap-3 lg:w-4/12 pt-2 sm:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-4">
                        <!-- Berlaku Mulai Badge -->
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block uppercase tracking-wider">Berlaku Mulai</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-0.5">
                                <i class="ti ti-calendar text-xs"></i>
                                <span>{{ $bulanTeks }} {{ $tahunTeks }}</span>
                            </span>
                        </div>

                        <!-- Action Buttons: Mutasi & Keluarkan -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            {{-- Tombol Mutasi --}}
                            @can('periodegaji.setting')
                                <button type="button" 
                                        class="btnMutasi inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/80 text-xs font-bold transition active:scale-95 cursor-pointer shadow-2xs"
                                        data-nik="{{ $item->nik }}" 
                                        data-nama="{{ $k->nama_karyawan ?? '' }}"
                                        data-periode="{{ $item->kode_periode_gaji }}"
                                        title="Mutasi / Pindah ke Periode Gaji Lain">
                                    <i class="ti ti-arrows-left-right text-xs"></i>
                                    <span>Mutasi</span>
                                </button>
                            @endcan

                            {{-- Tombol Keluarkan --}}
                            @can('periodegaji.setting')
                                <form action="{{ route('periodegaji.removekaryawan', $item->id) }}" method="POST" class="deleteform m-0 inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btnDeleteAssignment p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                            title="Keluarkan dari Periode Gaji Ini">
                                        <i class="ti ti-trash text-xs"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="p-12 text-center bg-white rounded-2xl border border-slate-200/90 shadow-2xs text-slate-400">
                <i class="ti ti-users-off text-4xl text-slate-300 mb-2 d-block"></i>
                <p class="text-sm font-bold text-slate-700">Belum ada karyawan yang terdaftar di periode ini</p>
                <p class="text-xs text-slate-400 mt-1 mb-4">Gunakan tombol di bawah untuk menambahkan karyawan secara massal ke periode ini.</p>
                <button type="button" onclick="$('#btnOpenModalAdd').click()" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-user-plus text-sm"></i>
                    <span>Tambah Karyawan Sekarang</span>
                </button>
            </div>
        @endforelse
    </div>

</div>

<!-- ================= MODAL TAMBAH KARYAWAN MASSAL STANDAR V2 ================= -->
<div id="modalAddKaryawan" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <form action="{{ route('periodegaji.assign', $periode_gaji->kode_periode_gaji) }}" method="POST" id="formAssignMassal" class="m-0 flex flex-col h-full overflow-hidden">
            @csrf

            <!-- Modal Header -->
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative shrink-0"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-user-plus text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight">
                            Tambah Karyawan ke Periode Gaji
                        </h3>
                        <p class="text-[11px] text-slate-500">
                            Pilih karyawan dan tentukan bulan & tahun mulai berlaku
                        </p>
                    </div>
                </div>
                <button type="button" class="btnCloseModalAdd w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                    <i class="ti ti-x text-base"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4">
                <!-- Row Pilihan Bulan & Tahun Mulai Berlaku -->
                <div class="p-4 rounded-2xl border border-emerald-200/70 bg-emerald-50/50 space-y-3">
                    <div class="flex items-center gap-2">
                        <i class="ti ti-calendar-stats text-emerald-700 text-base"></i>
                        <span class="text-xs font-extrabold text-emerald-900">Periode Mulai Berlaku Payroll</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                Bulan Mulai Berlaku <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                                    <i class="ti ti-calendar-month text-sm"></i>
                                </span>
                                <select name="bulan" id="modal_bulan" required
                                        class="w-full pl-9 pr-3.5 py-2 text-xs bg-white border border-slate-300/90 rounded-xl text-slate-800 font-bold shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                            {{ getNamabulan($m) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                Tahun Mulai Berlaku <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                                    <i class="ti ti-calendar text-sm"></i>
                                </span>
                                <select name="tahun" id="modal_tahun" required
                                        class="w-full pl-9 pr-3.5 py-2 text-xs bg-white border border-slate-300/90 rounded-xl text-slate-800 font-bold shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                                    @for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toolbar Filter Pencarian Dalam Modal -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
                    <div class="sm:col-span-6 relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-search text-sm"></i>
                        </span>
                        <input type="text" id="modalSearch" placeholder="Ketik nama atau NIK..." 
                               class="w-full pl-9 pr-3.5 py-2 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 placeholder-slate-400 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                    </div>
                    <div class="sm:col-span-3">
                        <select id="modalFilterCabang" class="w-full px-3 py-2 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <select id="modalFilterDept" class="w-full px-3 py-2 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Select All Bar -->
                <div class="flex items-center justify-between px-1 py-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="checkAllModal" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">Pilih Semua Karyawan yang Tampil</span>
                    </label>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200" id="selectedCountBadge">
                        <span id="selectedCount">0</span> Terpilih
                    </span>
                </div>

                <!-- Container List Available Karyawan -->
                <div id="containerAvailableKaryawan" class="space-y-2 max-h-[320px] overflow-y-auto pr-1 custom-scrollbar">
                    <div class="py-12 text-center text-slate-400">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                        <p class="text-xs font-semibold">Memuat data karyawan...</p>
                    </div>
                </div>

                <p class="text-[11px] text-slate-400">
                    <i class="ti ti-info-circle me-1"></i>Hanya menampilkan karyawan aktif yang belum terdaftar di periode gaji manapun.
                </p>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 sm:px-6 py-3.5 border-t border-slate-100 bg-slate-50 flex items-center justify-between shrink-0">
                <span class="text-[11px] text-slate-500 hidden sm:inline" id="footerSummary">
                    Pilih minimal 1 karyawan untuk menyimpan.
                </span>
                <div class="flex items-center gap-2 ml-auto">
                    <button type="button" class="btnCloseModalAdd px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 text-xs font-bold transition active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitAssignMassal" disabled
                            class="px-5 py-2 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-check text-sm"></i>
                        <span>Masukkan ke Periode (<span id="submitSelectedCount">0</span>)</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL MUTASI KARYAWAN STANDAR V2 ================= -->
<div id="modalMutasi" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <form action="{{ route('periodegaji.mutasi') }}" method="POST" id="formMutasi" class="m-0 space-y-0" novalidate>
            @csrf

            <!-- Modal Header -->
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative shrink-0"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs bg-amber-500 text-white">
                        <i class="ti ti-arrows-left-right text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight">
                            Mutasi Periode Gaji
                        </h3>
                        <p class="text-[11px] text-slate-500">
                            Pindahkan karyawan ke siklus periode penggajian lain
                        </p>
                    </div>
                </div>
                <button type="button" class="btnCloseModalMutasi w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                    <i class="ti ti-x text-base"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4">
                <!-- Banner Info -->
                <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200/80 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm shrink-0">
                        <i class="ti ti-info-circle"></i>
                    </div>
                    <div>
                        <h5 class="text-xs font-bold text-slate-800">Ketentuan Mutasi Periode</h5>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                            Karyawan akan dipindahkan ke periode baru mulai bulan & tahun efektif yang dipilih. Riwayat mutasi akan tercatat di sistem.
                        </p>
                    </div>
                </div>

                <!-- Karyawan (Readonly) -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Karyawan</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-user text-base"></i>
                        </span>
                        <input type="hidden" name="nik" id="mutasi_nik">
                        <input type="text" id="mutasi_nama" readonly disabled
                               class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-700 bg-slate-100/80 border border-slate-200 rounded-xl cursor-not-allowed shadow-2xs">
                    </div>
                </div>

                <!-- Periode Tujuan Baru -->
                <div class="space-y-1.5">
                    <label for="kode_periode_baru" class="block text-xs font-bold text-slate-700">
                        Pindah ke Periode Baru <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-calendar-share text-base"></i>
                        </span>
                        <select name="kode_periode_baru" id="kode_periode_baru"
                                class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition shadow-2xs cursor-pointer">
                            <option value="">Pilih Periode Tujuan</option>
                            @foreach ($all_periode as $p)
                                @if ($p->kode_periode_gaji != $periode_gaji->kode_periode_gaji)
                                    <option value="{{ $p->kode_periode_gaji }}">
                                        {{ $p->kode_periode_gaji }} - {{ $p->nama_periode }} (Tgl {{ $p->tanggal_dari }} s/d {{ $p->tanggal_sampai }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_periode_baru"></div>
                </div>

                <!-- Bulan & Tahun Mulai Berlaku Mutasi -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label for="mutasi_bulan" class="block text-xs font-bold text-slate-700">
                            Bulan Berlaku <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                                <i class="ti ti-calendar-month text-base"></i>
                            </span>
                            <select name="bulan" id="mutasi_bulan"
                                    class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition shadow-2xs cursor-pointer">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == (date('n') == 12 ? 1 : date('n') + 1) ? 'selected' : '' }}>
                                        {{ getNamabulan($m) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_mutasi_bulan"></div>
                    </div>
                    <div class="space-y-1.5">
                        <label for="mutasi_tahun" class="block text-xs font-bold text-slate-700">
                            Tahun Berlaku <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                                <i class="ti ti-calendar text-base"></i>
                            </span>
                            <select name="tahun" id="mutasi_tahun"
                                    class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition shadow-2xs cursor-pointer">
                                @for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                    <option value="{{ $y }}" {{ $y == (date('n') == 12 ? date('Y') + 1 : date('Y')) ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_mutasi_tahun"></div>
                    </div>
                </div>

                <!-- Keterangan / Alasan -->
                <div class="space-y-1.5">
                    <label for="mutasi_keterangan" class="block text-xs font-bold text-slate-700">Keterangan / Alasan (Opsional)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-notes text-base"></i>
                        </span>
                        <input type="text" 
                               name="keterangan" 
                               id="mutasi_keterangan" 
                               maxlength="100"
                               placeholder="Contoh: Mutasi ke divisi operasional lapangan"
                               class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 sm:px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
                <button type="button" class="btnCloseModalMutasi px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 text-xs font-bold transition active:scale-95 cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        id="btnSubmitMutasi"
                        class="px-5 py-2.5 text-white font-bold rounded-xl text-xs shadow-md transition active:scale-95 cursor-pointer hover:opacity-95 flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700">
                    <i class="ti ti-arrows-left-right text-sm"></i>
                    <span>Proses Mutasi</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        const kodePeriode = "{{ $periode_gaji->kode_periode_gaji }}";

        // === MODAL TAMBAH KARYAWAN MASSAL ===
        let searchTimeout = null;

        function openModalAdd() {
            $('#modalSearch').val('');
            $('#modalFilterCabang').val('');
            $('#modalFilterDept').val('');
            $('#modalAddKaryawan').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            loadAvailableKaryawan();
        }

        function closeModalAdd() {
            $('#modalAddKaryawan').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        $('#btnOpenModalAdd').on('click', openModalAdd);
        $('.btnCloseModalAdd').on('click', closeModalAdd);

        function loadAvailableKaryawan() {
            const q = $('#modalSearch').val();
            const cabang = $('#modalFilterCabang').val();
            const dept = $('#modalFilterDept').val();

            $('#containerAvailableKaryawan').html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat data karyawan...</p>
                </div>
            `);

            $.ajax({
                url: `/periodegaji/${kodePeriode}/get-available-karyawan`,
                type: 'GET',
                data: { q: q, kode_cabang: cabang, kode_dept: dept },
                success: function(response) {
                    if (response.status === 'success' && response.data.length > 0) {
                        let html = '';
                        response.data.forEach((k) => {
                            const namaCabang = k.cabang ? k.cabang.nama_cabang : (k.kode_cabang || '-');
                            const namaDept = k.departemen ? k.departemen.nama_dept : (k.kode_dept || '-');
                            const namaJabatan = k.jabatan ? k.jabatan.nama_jabatan : '-';
                            const fotoUrl = k.foto_url || "{{ asset('assets/img/avatars/No_Image_Available.jpg') }}";
                            const statusText = k.status_karyawan_text || '';
                            const statusBadge = k.status_karyawan === 'T' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-sky-50 text-sky-700 border-sky-200';
                            const statusHtml = statusText ? `<span class="px-1.5 py-0.5 rounded text-[10px] font-bold border ${statusBadge}">${statusText}</span>` : '';
                            const upahHtml = k.jenis_upah ? `<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">${k.jenis_upah}</span>` : '';

                            html += `
                                <div class="employee-card bg-slate-50/60 hover:bg-slate-100/80 border border-slate-200/80 rounded-xl p-3 transition-all duration-150 cursor-pointer flex items-center justify-between gap-3 select-none" data-nik="${k.nik}">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <input type="checkbox" name="nik[]" value="${k.nik}" class="check-karyawan rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer shrink-0">
                                        <img src="${fotoUrl}" alt="Foto" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shrink-0">
                                        <div class="min-w-0">
                                            <h5 class="text-xs font-black text-slate-900 truncate">${k.nama_karyawan}</h5>
                                            <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                                <span class="font-mono text-[10px] font-bold px-1.5 py-0.2 bg-white rounded border border-slate-200 text-slate-700">${k.nik}</span>
                                                ${statusHtml}
                                                ${upahHtml}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hidden sm:block text-right shrink-0">
                                        <span class="text-xs font-bold text-slate-800 block truncate max-w-[180px]">${namaCabang}</span>
                                        <span class="text-[11px] text-slate-500 block truncate max-w-[180px]">${namaDept} &bull; ${namaJabatan}</span>
                                    </div>
                                </div>
                            `;
                        });
                        $('#containerAvailableKaryawan').html(html);
                    } else {
                        $('#containerAvailableKaryawan').html(`
                            <div class="p-8 text-center bg-slate-50/60 rounded-xl border border-dashed border-slate-200 text-slate-400">
                                <i class="ti ti-search-off text-3xl mb-1 d-block"></i>
                                <p class="text-xs font-bold text-slate-600">Tidak ada karyawan yang sesuai</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Semua karyawan aktif sudah memiliki periode gaji atau tidak cocok dengan filter.</p>
                            </div>
                        `);
                    }
                    $('#checkAllModal').prop('checked', false);
                    updateSelectedCount();
                },
                error: function() {
                    $('#containerAvailableKaryawan').html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs text-center font-bold">
                            Gagal memuat data karyawan. Silakan coba lagi.
                        </div>
                    `);
                }
            });
        }

        // Trigger Filter Modal Search
        $('#modalSearch').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(loadAvailableKaryawan, 300);
        });
        $('#modalFilterCabang, #modalFilterDept').on('change', function() {
            loadAvailableKaryawan();
        });

        // Check All
        $('#checkAllModal').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.check-karyawan').prop('checked', isChecked).trigger('change');
        });

        // Click on employee card to toggle checkbox
        $(document).on('click', '.employee-card', function(e) {
            if ($(e.target).is('input[type="checkbox"]')) return;
            const cb = $(this).find('.check-karyawan');
            cb.prop('checked', !cb.prop('checked')).trigger('change');
        });

        $(document).on('change', '.check-karyawan', function() {
            const total = $('.check-karyawan').length;
            const checked = $('.check-karyawan:checked').length;
            $('#checkAllModal').prop('checked', total > 0 && total === checked);

            $('.check-karyawan').each(function() {
                const card = $(this).closest('.employee-card');
                if ($(this).is(':checked')) {
                    card.addClass('border-emerald-500 bg-emerald-50/60');
                } else {
                    card.removeClass('border-emerald-500 bg-emerald-50/60');
                }
            });

            updateSelectedCount();
        });

        function updateSelectedCount() {
            const count = $('.check-karyawan:checked').length;
            $('#selectedCount').text(count);
            $('#submitSelectedCount').text(count);
            if (count > 0) {
                $('#btnSubmitAssignMassal').prop('disabled', false);
                $('#selectedCountBadge').removeClass('bg-slate-100 text-slate-700').addClass('bg-emerald-600 text-white border-transparent');
            } else {
                $('#btnSubmitAssignMassal').prop('disabled', true);
                $('#selectedCountBadge').removeClass('bg-emerald-600 text-white border-transparent').addClass('bg-slate-100 text-slate-700');
            }
        }

        // === MODAL MUTASI ===
        function openModalMutasi(nik, nama) {
            $('#mutasi_nik').val(nik);
            $('#mutasi_nama').val(nik + ' - ' + nama);
            $('#kode_periode_baru').val('');
            $('#mutasi_keterangan').val('');
            $('#modalMutasi').removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeModalMutasi() {
            $('#modalMutasi').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        $(document).on('click', '.btnMutasi', function(e) {
            e.preventDefault();
            let nik = $(this).data('nik');
            let nama = $(this).data('nama');
            openModalMutasi(nik, nama);
        });

        $('.btnCloseModalMutasi').on('click', closeModalMutasi);

        // Backdrop & ESC key closing
        $('#modalAddKaryawan, #modalMutasi').click(function(e) {
            if (e.target === this) {
                closeModalAdd();
                closeModalMutasi();
            }
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape') {
                if (!$('#modalAddKaryawan').hasClass('hidden')) closeModalAdd();
                if (!$('#modalMutasi').hasClass('hidden')) closeModalMutasi();
            }
        });

        // === FORM MUTASI SUBMIT VALIDATION ===
        $('#formMutasi').on('submit', function(e) {
            const periodeBaru = $('#kode_periode_baru').val();
            const bulan = $('#mutasi_bulan').val();
            const tahun = $('#mutasi_tahun').val();
            let hasError = false;

            // Reset error states
            $('#formMutasi .error-feedback').addClass('hidden').text('');
            $('#formMutasi select').removeClass('border-rose-500');

            if (!periodeBaru) {
                $('#err_kode_periode_baru').text('Pilih periode tujuan mutasi').removeClass('hidden');
                $('#kode_periode_baru').addClass('border-rose-500');
                hasError = true;
            }

            if (!bulan) {
                $('#err_mutasi_bulan').text('Pilih bulan berlaku').removeClass('hidden');
                $('#mutasi_bulan').addClass('border-rose-500');
                hasError = true;
            }

            if (!tahun) {
                $('#err_mutasi_tahun').text('Pilih tahun berlaku').removeClass('hidden');
                $('#mutasi_tahun').addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitMutasi');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Memproses...</span>');
        });

        // === DELETE CONFIRMATION (KELUARKAN KARYAWAN) ===
        $(document).on('click', '.btnDeleteAssignment', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Keluarkan Karyawan?',
                text: 'Karyawan akan dikeluarkan dari siklus periode gaji ini!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0a5c41',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Keluarkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
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
        });
    });
</script>
@endpush
