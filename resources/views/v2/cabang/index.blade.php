@extends('v2.layouts.app')

@section('titlepage', 'Cabang')
@section('page_title', 'Data Cabang')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalCabang = $cabang->total() ?? $cabang->count();
    $totalKaryawan = $total_karyawan ?? $cabang->sum('karyawan_count');
@endphp

<div class="space-y-5" x-data="{ viewMode: 'grid' }">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-building-community text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Cabang</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola kantor cabang, penempatan unit kerja, geofence radius & koordinat GPS presensi
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
                <span class="font-bold text-slate-800">Cabang</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('cabang.create')
                    <!-- Tambah Cabang -->
                    <button type="button" id="btncreateCabang" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Cabang</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. EXECUTIVE METRICS CARD ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-building-community text-[190px]"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            <!-- Segment 1: Total Cabang -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Cabang
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Lokasi
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalCabang) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-map-pin-check text-xs opacity-80"></i>
                        <span>Kantor Operasional</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        Aktif
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Total Karyawan Terpetakan -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Karyawan Terdaftar
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Pegawai
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalKaryawan) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-users text-xs opacity-80"></i>
                        <span>Total Personel Cabang</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        100%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. FILTER TOOLBAR & VIEW TOGGLE (CONSISTENT V2) ================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs">
        <form method="GET" action="{{ route('cabang.index') }}" id="filterCabangForm">
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                
                <!-- Search Box -->
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" 
                           name="nama_cabang" 
                           id="searchCabang"
                           value="{{ request('nama_cabang') }}" 
                           placeholder="Cari kode, nama cabang, atau alamat..." 
                           class="w-full h-10 pl-10 pr-10 text-xs sm:text-sm font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                    
                    @if(request('nama_cabang'))
                        <a href="{{ route('cabang.index') }}" 
                           class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition flex items-center"
                           title="Reset pencarian">
                            <i class="ti ti-x text-sm"></i>
                        </a>
                    @endif
                </div>

                <!-- Right Side: Action Buttons & View Mode -->
                <div class="flex items-center gap-2 justify-end">
                    <button type="submit" 
                            class="h-10 px-4 text-xs font-bold text-white rounded-xl shadow-2xs flex items-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>

                    @if(request('nama_cabang'))
                        <a href="{{ route('cabang.index') }}" 
                           class="h-10 px-3 border border-slate-200 text-slate-600 hover:bg-slate-100 rounded-xl text-xs font-bold flex items-center gap-1 transition"
                           title="Reset Filter">
                            <i class="ti ti-refresh text-sm"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    @endif

                    <!-- Dual-View Toggle (Table vs Grid) -->
                    <div class="flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200 ml-1">
                        <button type="button" 
                                @click="viewMode = 'list'" 
                                :class="viewMode === 'list' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-400 hover:text-slate-600'" 
                                class="p-1.5 rounded-lg transition-all text-sm flex items-center justify-center cursor-pointer"
                                title="Tampilan Tabel">
                            <i class="ti ti-list"></i>
                        </button>
                        <button type="button" 
                                @click="viewMode = 'grid'" 
                                :class="viewMode === 'grid' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-400 hover:text-slate-600'" 
                                class="p-1.5 rounded-lg transition-all text-sm flex items-center justify-center cursor-pointer"
                                title="Tampilan Grid Card">
                            <i class="ti ti-layout-grid"></i>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- ================= 4. MAIN DATA DISPLAY (TABLE & GRID CARD VIEW) ================= -->
    <div>
        <!-- VIEW MODE: TABLE LIST -->
        <div x-show="viewMode === 'list'" x-cloak>
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                
                <!-- Table Header Bar -->
                <div class="px-5 py-3.5 flex items-center justify-between text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <div class="flex items-center gap-2.5">
                        <i class="ti ti-building-community text-lg"></i>
                        <span class="text-xs sm:text-sm font-extrabold tracking-wide">Daftar Kantor Cabang</span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-white/20 text-white border border-white/30 backdrop-blur-xs">
                        {{ $cabang->total() ?? $cabang->count() }} Data
                    </span>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[950px]">
                        <thead>
                            <tr class="text-white border-b border-white/20 text-[11px] font-black uppercase tracking-wider"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <th class="py-3 px-3 text-center w-12 whitespace-nowrap">NO</th>
                                <th class="py-3 px-3 w-20 whitespace-nowrap">KODE</th>
                                <th class="py-3 px-4 min-w-[240px]">NAMA & ALAMAT CABANG</th>
                                <th class="py-3 px-4 w-36 whitespace-nowrap">TELEPON</th>
                                <th class="py-3 px-4 min-w-[260px]">GEOFENCE & TIMEZONE</th>
                                <th class="py-3 px-3 text-center w-36 whitespace-nowrap">KARYAWAN</th>
                                <th class="py-3 px-3 text-center w-24 whitespace-nowrap">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700 bg-white">
                            @forelse ($cabang as $d)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <!-- NO -->
                                    <td class="py-3 px-3 text-center font-bold text-slate-400 whitespace-nowrap">
                                        {{ ($cabang->currentPage() - 1) * $cabang->perPage() + $loop->iteration }}
                                    </td>

                                    <!-- KODE CABANG -->
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-black tracking-wider bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $d->kode_cabang }}
                                        </span>
                                    </td>

                                    <!-- NAMA & ALAMAT CABANG -->
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0 shadow-2xs"
                                                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                                <i class="ti ti-building"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h6 class="font-bold text-slate-900 text-xs sm:text-sm group-hover:text-emerald-700 transition-colors mb-0">
                                                    {{ $d->nama_cabang }}
                                                </h6>
                                                <p class="text-[11px] text-slate-400 mb-0 flex items-center gap-1 mt-0.5" title="{{ $d->alamat_cabang }}">
                                                    <i class="ti ti-map-pin text-[10px] shrink-0 text-slate-400"></i>
                                                    <span>{{ $d->alamat_cabang }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- TELEPON -->
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="font-mono text-slate-700 text-xs inline-flex items-center gap-1.5">
                                            <i class="ti ti-phone text-slate-400 text-sm"></i>
                                            <span>{{ $d->telepon_cabang ?: '-' }}</span>
                                        </span>
                                    </td>

                                    <!-- GEOFENCE & TIMEZONE -->
                                    <td class="py-3 px-4">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[10.5px] font-bold bg-slate-100 text-slate-700 border border-slate-200 whitespace-nowrap" title="Radius Presensi">
                                                    <i class="ti ti-radar text-emerald-600"></i>
                                                    <span>{{ $d->radius_cabang }} meter</span>
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap">
                                                    <i class="ti ti-clock text-xs"></i>
                                                    <span>{{ $d->timezone }}</span>
                                                </span>
                                            </div>
                                            <span class="text-[10.5px] font-mono text-slate-400 cursor-pointer hover:text-emerald-700 transition inline-flex items-center gap-1" title="Koordinat GPS: {{ $d->lokasi_cabang }}">
                                                <i class="ti ti-location-filled text-rose-500 text-[10px] shrink-0"></i>
                                                <span>{{ $d->lokasi_cabang }}</span>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- JUMLAH KARYAWAN -->
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        @if(($d->karyawan_count ?? 0) > 0)
                                            <a href="{{ route('karyawan.index', ['kode_cabang' => $d->kode_cabang]) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs">
                                                <i class="ti ti-users text-xs"></i>
                                                <span>{{ $d->karyawan_count }} Karyawan</span>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-400 border border-slate-200">
                                                <i class="ti ti-user-x text-xs"></i>
                                                <span>0</span>
                                            </span>
                                        @endif
                                    </td>

                                    <!-- AKSI -->
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                            @can('cabang.edit')
                                                <button type="button" 
                                                        class="w-7 h-7 rounded-md hover:bg-white text-blue-600 hover:text-blue-700 flex items-center justify-center transition-all cursor-pointer editCabang"
                                                        kode_cabang="{{ Crypt::encrypt($d->kode_cabang) }}"
                                                        title="Edit Cabang">
                                                    <i class="ti ti-edit text-sm"></i>
                                                </button>
                                            @endcan

                                            @can('cabang.delete')
                                                <form action="{{ route('cabang.delete', Crypt::encrypt($d->kode_cabang)) }}" 
                                                      method="POST" 
                                                      class="m-0 deleteform">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="w-7 h-7 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition-all cursor-pointer delete-confirm"
                                                            title="Hapus Cabang">
                                                        <i class="ti ti-trash text-sm"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                 </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                                                <i class="ti ti-building-community"></i>
                                            </div>
                                            <p class="text-xs font-bold text-slate-600 mb-0">Tidak ada data cabang ditemukan</p>
                                            <p class="text-[11px] text-slate-400 mb-0">Coba ubah kata kunci pencarian atau tambahkan cabang baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                @if($cabang->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between flex-wrap gap-3">
                        <div class="text-xs text-slate-500 font-medium">
                            Menampilkan <span class="font-bold text-slate-700">{{ $cabang->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-slate-700">{{ $cabang->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-700">{{ $cabang->total() }}</span> cabang
                        </div>
                        <div>
                            {{ $cabang->links('v2.components.pagination') }}
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- VIEW MODE: GRID / FULL WIDTH CARDS -->
        <div x-show="viewMode === 'grid'" x-cloak class="space-y-3">
            @forelse ($cabang as $d)
                <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-emerald-300/80 p-4 sm:p-5 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 group">
                    
                    <!-- Left Section: Avatar + Title, Code, Timezone + Address -->
                    <div class="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl flex items-center justify-center text-lg font-black text-white shrink-0 shadow-xs transition-transform group-hover:scale-105"
                             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-building-community"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $d->kode_cabang }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="ti ti-clock text-xs"></i>
                                    <span>{{ $d->timezone }}</span>
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="ti ti-radar text-blue-500"></i>
                                    <span>{{ $d->radius_cabang }}m radius</span>
                                </span>
                            </div>
                            <h3 class="text-sm sm:text-base font-extrabold text-slate-900 group-hover:text-emerald-700 transition truncate mb-0.5">
                                {{ $d->nama_cabang }}
                            </h3>
                            <p class="text-xs text-slate-500 flex items-center gap-1.5 line-clamp-1 mb-0" title="{{ $d->alamat_cabang }}">
                                <i class="ti ti-map-pin text-rose-500 text-xs shrink-0"></i>
                                <span class="truncate">{{ $d->alamat_cabang }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Middle Section: Phone, GPS Location & Assigned Personnel -->
                    <div class="flex items-center flex-wrap sm:flex-nowrap gap-3 sm:gap-6 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                        <!-- Phone & GPS -->
                        <div class="flex flex-col gap-1 min-w-[140px]">
                            <div class="flex items-center gap-1.5 text-xs text-slate-700 font-medium">
                                <i class="ti ti-phone text-slate-400 text-sm"></i>
                                <span>{{ $d->telepon_cabang ?: '-' }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-[11px] font-mono text-slate-400 truncate max-w-[160px]" title="Koordinat GPS: {{ $d->lokasi_cabang }}">
                                <i class="ti ti-location-filled text-rose-500 text-[11px] shrink-0"></i>
                                <span class="truncate">{{ $d->lokasi_cabang }}</span>
                            </div>
                        </div>

                        <!-- Karyawan Badge Link -->
                        <div class="shrink-0">
                            @if(($d->karyawan_count ?? 0) > 0)
                                <a href="{{ route('karyawan.index', ['kode_cabang' => $d->kode_cabang]) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs hover:border-emerald-300">
                                    <i class="ti ti-users text-sm"></i>
                                    <span>{{ $d->karyawan_count }} Karyawan</span>
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-slate-50 text-slate-400 border border-slate-200">
                                    <i class="ti ti-user-x text-sm"></i>
                                    <span>0 Karyawan</span>
                                </span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            @can('cabang.edit')
                                <button type="button" 
                                        class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 border border-blue-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs editCabang"
                                        kode_cabang="{{ Crypt::encrypt($d->kode_cabang) }}"
                                        title="Edit Cabang">
                                    <i class="ti ti-edit text-xs"></i>
                                </button>
                            @endcan

                            @can('cabang.delete')
                                <form action="{{ route('cabang.delete', Crypt::encrypt($d->kode_cabang)) }}" 
                                      method="POST" 
                                      class="m-0 deleteform">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 border border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs delete-confirm"
                                            title="Hapus Cabang">
                                        <i class="ti ti-trash text-xs"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>

                </div>
            @empty
                <div class="py-12 text-center text-slate-400 bg-white rounded-2xl border border-slate-200">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                            <i class="ti ti-building-community"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-600 mb-0">Tidak ada data cabang ditemukan</p>
                        <p class="text-[11px] text-slate-400 mb-0">Coba ubah kata kunci pencarian atau tambahkan cabang baru.</p>
                    </div>
                </div>
            @endforelse
        </div>

            <!-- Pagination Footer for Grid -->
            @if($cabang->hasPages())
                <div class="mt-5 p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between flex-wrap gap-3">
                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan <span class="font-bold text-slate-700">{{ $cabang->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-slate-700">{{ $cabang->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-700">{{ $cabang->total() }}</span> cabang
                    </div>
                    <div>
                        {{ $cabang->links('v2.components.pagination') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

<!-- ================= MODAL CONTAINER ================= -->
<div class="modal fade" id="modalCabangV2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-building-community"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalCabangTitle">Tambah Kantor Cabang</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalCabangSubtitle">Konfigurasi data cabang, alamat, timezone & titik presensi GPS</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6" id="loadModalContent">
                <!-- AJAX Content Loaded Here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<!-- Leaflet CSS & JS for Interactive Map in Modal -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    $(function() {
        const modalEl = document.getElementById('modalCabangV2');
        const modalInstance = new bootstrap.Modal(modalEl);

        function showLoading() {
            $("#loadModalContent").html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="w-8 h-8 border-4 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mx-auto mb-2.5"></div>
                    <p class="text-xs font-medium">Memuat formulir cabang...</p>
                </div>
            `);
        }

        // Button Tambah Cabang
        $("#btncreateCabang").click(function() {
            $("#modalCabangTitle").text("Tambah Kantor Cabang");
            $("#modalCabangSubtitle").text("Konfigurasi data cabang, alamat, timezone & titik presensi GPS");
            showLoading();
            modalInstance.show();
            $("#loadModalContent").load("{{ route('cabang.create') }}");
        });

        // Button Edit Cabang
        $(document).on("click", ".editCabang", function() {
            const kode_cabang = $(this).attr("kode_cabang");
            $("#modalCabangTitle").text("Edit Data Cabang");
            $("#modalCabangSubtitle").text("Perbarui informasi cabang, radius geofence & koordinat peta");
            showLoading();
            modalInstance.show();
            $("#loadModalContent").load(`/cabang/${kode_cabang}`);
        });
    });
</script>
@endpush
