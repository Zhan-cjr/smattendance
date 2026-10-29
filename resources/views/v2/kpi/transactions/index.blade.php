@extends('v2.layouts.app')

@section('titlepage', 'Transaksi KPI')
@section('page_title', 'Transaksi KPI')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $user = auth()->user();
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-chart-bar text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Transaksi KPI Karyawan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola target, input realisasi, dan evaluasi kinerja karyawan pada periode KPI aktif
            </p>
        </div>

        <!-- Breadcrumbs & Status Periode -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500">KPI</span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Transaksi</span>
            </nav>

            <!-- Periode Status Indicator -->
            @if ($active_period)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-emerald-200/80 bg-emerald-50 text-emerald-800 text-xs font-semibold shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Periode Aktif: <strong>{{ $active_period->nama_periode }}</strong></span>
                    <span class="text-emerald-600 font-normal">({{ date('d M Y', strtotime($active_period->start_date)) }} - {{ date('d M Y', strtotime($active_period->end_date)) }})</span>
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-rose-200/80 bg-rose-50 text-rose-800 text-xs font-semibold shadow-2xs">
                    <i class="ti ti-alert-triangle text-sm text-rose-500"></i>
                    <span>Belum ada Periode KPI yang Aktif</span>
                </div>
            @endif
        </div>
    </div>

    <!-- ================= 2. FILTER & SEARCH TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('kpi.transactions.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Nama / NIK -->
                <div class="lg:col-span-4 relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" name="nama_karyawan" value="{{ request('nama_karyawan') }}"
                           placeholder="Cari nama karyawan / NIK..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium"
                           style="--tw-ring-color: {{ $themeColor1 }};">
                </div>

                <!-- Cabang Filter -->
                <div class="lg:col-span-2">
                    <select name="kode_cabang"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium cursor-pointer"
                            style="--tw-ring-color: {{ $themeColor1 }};">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
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
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jabatan Filter -->
                <div class="lg:col-span-2">
                    <select name="kode_jabatan"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium cursor-pointer"
                            style="--tw-ring-color: {{ $themeColor1 }};">
                        <option value="">Semua Jabatan</option>
                        @foreach ($jabatan as $j)
                            <option value="{{ $j->kode_jabatan }}" {{ request('kode_jabatan') == $j->kode_jabatan ? 'selected' : '' }}>
                                {{ $j->nama_jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Button Actions -->
                <div class="lg:col-span-1 flex gap-1.5">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center p-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});"
                            title="Terapkan Filter">
                        <i class="ti ti-filter text-base"></i>
                    </button>
                    @if(request()->hasAny(['nama_karyawan', 'kode_cabang', 'kode_dept', 'kode_jabatan']))
                        <a href="{{ route('kpi.transactions.index') }}"
                           class="inline-flex items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800 text-xs font-bold transition shadow-2xs cursor-pointer"
                           title="Reset Filter">
                            <i class="ti ti-refresh text-base"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- ================= 3. TRANSACTION CARD LIST (FULL WIDTH) ================= -->
    <div class="space-y-3">
        @forelse ($karyawan as $item)
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 transition-all duration-200 hover:border-slate-300 hover:shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Left: Profile Karyawan -->
                    <div class="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
                        @if(!empty($item->foto) && Storage::disk('public')->exists('/karyawan/' . $item->foto))
                            <img src="{{ getfotoKaryawan($item->foto) }}" 
                                 alt="{{ $item->nama_karyawan }}" 
                                 class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0 shadow-2xs">
                        @else
                            <div class="w-12 h-12 rounded-xl text-white font-bold flex items-center justify-center text-lg shrink-0 shadow-2xs"
                                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                {{ substr($item->nama_karyawan, 0, 1) }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm sm:text-base font-bold text-slate-800 truncate">
                                    {{ $item->nama_karyawan }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                                    {{ $item->nik }}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-slate-500 font-medium">
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-briefcase text-slate-400"></i>
                                    {{ $item->nama_jabatan }}
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-building text-slate-400"></i>
                                    {{ $item->nama_dept }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Center: Metrik Nilai, Grade, Status -->
                    <div class="grid grid-cols-3 sm:flex items-center gap-3 sm:gap-6 py-3 lg:py-0 border-y lg:border-y-0 lg:border-x border-slate-100 lg:px-6">
                        <!-- Status KPI -->
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status KPI</span>
                            @if (empty($item->kpi_status))
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Belum Diset
                                </span>
                            @elseif ($item->kpi_status == 'draft')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Draft
                                </span>
                            @elseif ($item->kpi_status == 'submitted')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                    Submitted
                                </span>
                            @elseif ($item->kpi_status == 'approved')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Approved
                                </span>
                            @endif
                        </div>

                        <!-- Nilai Skor -->
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Nilai</span>
                            <span class="text-sm font-bold text-slate-800">
                                {{ !empty($item->total_nilai) ? number_format($item->total_nilai, 2) : '-' }}
                            </span>
                        </div>

                        <!-- Grade -->
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Grade</span>
                            @if(!empty($item->grade))
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-extrabold text-white shadow-2xs"
                                      style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                    {{ $item->grade }}
                                </span>
                            @else
                                <span class="text-sm font-semibold text-slate-400">-</span>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Action Buttons -->
                    <div class="flex items-center justify-end gap-2 shrink-0">
                        @if ($active_period)
                            @if (empty($item->kpi_id))
                                <a href="{{ route('kpi.transactions.settarget', $item->nik) }}?{{ http_build_query(request()->query()) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                                   style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                    <i class="ti ti-target text-sm"></i>
                                    <span>Set Target</span>
                                </a>
                            @else
                                <a href="{{ route('kpi.transactions.show', $item->kpi_id) }}?{{ http_build_query(request()->query()) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-xs cursor-pointer active:scale-95">
                                    <i class="ti ti-file-analytics text-sm"></i>
                                    <span>Lihat KPI</span>
                                </a>
                                <a href="{{ route('kpi.transactions.print', $item->kpi_id) }}" target="_blank"
                                   class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                   title="Cetak Laporan">
                                    <i class="ti ti-printer text-base"></i>
                                </a>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-400 text-xs font-medium border border-slate-200">
                                <i class="ti ti-lock text-sm"></i>
                                <span>Periode Non-Aktif</span>
                            </span>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/90 p-8 sm:p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <i class="ti ti-users-minus text-3xl"></i>
                </div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800">Tidak ada data karyawan ditemukan</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                    Coba sesuaikan kata kunci pencarian atau reset filter cabang, departemen, dan jabatan.
                </p>
            </div>
        @endforelse
    </div>

    <!-- ================= 4. PAGINATION ================= -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
        <p class="text-xs text-slate-500 font-medium">
            Menampilkan {{ $karyawan->firstItem() ?? 0 }} - {{ $karyawan->lastItem() ?? 0 }} dari {{ $karyawan->total() }} data
        </p>
        <div>
            {{ $karyawan->links('pagination::tailwind') }}
        </div>
    </div>

</div>

@endsection
