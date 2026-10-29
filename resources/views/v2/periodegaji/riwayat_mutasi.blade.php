@extends('v2.layouts.app')

@section('titlepage', 'Riwayat Mutasi Periode Gaji')
@section('page_title', 'Riwayat Mutasi Periode Gaji')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalMutasi = $mutasi->total();
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
                    <span class="text-slate-700">Riwayat Mutasi</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 mt-0.5">
                    <i class="ti ti-history text-2xl" style="color: {{ $themeColor1 }};"></i>
                    <span>Log Mutasi Periode Penggajian</span>
                </h1>
            </div>
        </div>

        <!-- Breadcrumb Navigation -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('periodegaji.index') }}" class="hover:text-slate-700 transition">Periode Gaji</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Riwayat Mutasi</span>
            </nav>
        </div>
    </div>

    <!-- ================= 2. FILTER TOOLBAR STANDAR V2 ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('periodegaji.riwayatmutasi') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-2.5 sm:gap-3 items-center">
                
                <!-- Cari NIK Karyawan -->
                <div class="md:col-span-5 relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-search text-sm"></i>
                    </span>
                    <input type="text" name="nik" id="filter_nik" 
                           value="{{ request('nik') }}" 
                           placeholder="Cari NIK Karyawan..." 
                           class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 placeholder-slate-400 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                </div>

                <!-- Filter Periode Gaji -->
                <div class="md:col-span-5">
                    <select name="kode_periode" id="filter_kode_periode" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Periode Gaji</option>
                        @foreach ($all_periode as $p)
                            <option value="{{ $p->kode_periode_gaji }}" {{ request('kode_periode') == $p->kode_periode_gaji ? 'selected' : '' }}>
                                {{ $p->kode_periode_gaji }} - {{ $p->nama_periode }}
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
                    @if (request()->hasAny(['nik', 'kode_periode']))
                        <a href="{{ route('periodegaji.riwayatmutasi') }}" 
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
                <span>Total <strong>{{ $totalMutasi }}</strong> Log Riwayat Mutasi</span>
            </div>
        </div>
    </div>

    <!-- ================= 3. LIST CARD MUTASI PERIODE GAJI ================= -->
    <div class="space-y-3">
        @forelse ($mutasi as $m)
            @php
                $k = $m->karyawan;
                $foto = $k->foto ?? null;
                $fotoUrl = (!empty($foto) && Storage::disk('public')->exists('/karyawan/' . $foto))
                    ? getfotoKaryawan($foto)
                    : asset('assets/img/avatars/No_Image_Available.jpg');
            @endphp
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-slate-300 transition-all duration-200 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Left: Identity & Tanggal Efektif -->
                    <div class="flex items-center gap-3.5 min-w-0 lg:w-4/12">
                        <div class="relative shrink-0">
                            <img src="{{ $fotoUrl }}" alt="Foto" 
                                 class="w-12 h-12 rounded-xl object-cover border border-slate-200/80 shadow-2xs">
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs sm:text-sm font-black text-slate-900 truncate group-hover:text-emerald-700 transition-colors">
                                {{ $k->nama_karyawan ?? '-' }}
                            </h4>
                            <div class="flex items-center gap-2 flex-wrap mt-1 text-[11px] text-slate-500">
                                <span class="font-mono font-bold text-slate-700">{{ $m->nik }}</span>
                                <span>&bull;</span>
                                <span class="inline-flex items-center gap-1 text-slate-600 font-semibold">
                                    <i class="ti ti-calendar-event text-xs text-slate-400"></i>
                                    Efektif: <strong class="text-slate-800">{{ date('d M Y', strtotime($m->tanggal_efektif)) }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Middle: Transisi Periode (Lama -> Baru) -->
                    <div class="flex items-center gap-2 lg:w-5/12 pt-2 sm:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-4">
                        <!-- Periode Lama -->
                        <div class="flex-1 min-w-0 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-center">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Periode Asal</span>
                            @if ($m->periodeLama)
                                <span class="font-extrabold text-slate-800 text-xs block truncate" title="{{ $m->periodeLama->nama_periode }}">
                                    {{ $m->periodeLama->nama_periode }}
                                </span>
                                <span class="font-mono text-[10px] font-bold text-slate-500 block">({{ $m->kode_periode_lama }})</span>
                            @else
                                <span class="text-xs font-bold text-slate-500 block">Penempatan Awal</span>
                            @endif
                        </div>

                        <!-- Arrow Icon -->
                        <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200">
                            <i class="ti ti-arrow-right text-sm"></i>
                        </div>

                        <!-- Periode Baru -->
                        <div class="flex-1 min-w-0 p-2.5 rounded-xl bg-emerald-50/50 border border-emerald-200 text-center">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block mb-0.5">Periode Tujuan</span>
                            <span class="font-extrabold text-emerald-950 text-xs block truncate" title="{{ $m->periodeBaru->nama_periode ?? '-' }}">
                                {{ $m->periodeBaru->nama_periode ?? '-' }}
                            </span>
                            <span class="font-mono text-[10px] font-bold text-emerald-700 block">({{ $m->kode_periode_baru }})</span>
                        </div>
                    </div>

                    <!-- Right: Info Admin & Keterangan -->
                    <div class="flex flex-col justify-center text-right lg:w-3/12 pt-2 sm:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-4">
                        <span class="text-[11px] text-slate-700 font-semibold block truncate" title="{{ $m->keterangan ?? '-' }}">
                            <i class="ti ti-notes text-xs text-slate-400 me-0.5"></i>
                            {{ $m->keterangan ?? '-' }}
                        </span>
                        <div class="text-[10.5px] text-slate-400 mt-1">
                            <span>Oleh: <strong class="text-slate-600">{{ $m->user->name ?? 'System' }}</strong></span>
                            <span class="mx-1">&bull;</span>
                            <span>{{ date('d/m/y H:i', strtotime($m->created_at)) }}</span>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="p-12 text-center bg-white rounded-2xl border border-slate-200/90 shadow-2xs text-slate-400">
                <i class="ti ti-history-off text-4xl text-slate-300 mb-2 d-block"></i>
                <p class="text-sm font-bold text-slate-700">Belum ada riwayat mutasi periode gaji</p>
                <p class="text-xs text-slate-400 mt-1">Riwayat mutasi perpindahan karyawan antar periode akan otomatis dicatat di sini.</p>
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="pt-2">
            {{ $mutasi->links('v2.components.pagination') }}
        </div>
    </div>

</div>

@endsection
