@extends('v2.layouts.app')

@section('titlepage', 'Grup')
@section('page_title', 'Data Grup Kerja')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalGrup = $grup->count();
    $totalKaryawanGrup = $grup->sum('karyawan_count');
@endphp

<div class="space-y-5" x-data="{ viewMode: 'list' }">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-users-group text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Grup Kerja</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola regu kerja (grup shift), jadwal presensi berkala, dan alokasi anggota tim
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
                <span class="font-bold text-slate-800">Grup</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('grup.create')
                    <!-- Tambah Grup -->
                    <button type="button" id="btnCreate" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Grup</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. EXECUTIVE METRICS CARD (SEAMLESS SOLID/GRADIENT CARD WITH TAPERED DIVIDERS) ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-users-group text-[190px]"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            <!-- Segment 1: Total Grup -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Grup
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Regu
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalGrup) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-users text-xs opacity-80"></i>
                        <span>Regu Kerja</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        Aktif
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Total Personel dalam Grup -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Anggota Terjadwal
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Pegawai
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalKaryawanGrup) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-user-check text-xs opacity-80"></i>
                        <span>Total Anggota Grup</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        100%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Rata-rata Personel per Grup -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Rata-Rata Personel
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Rasio
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ $totalGrup > 0 ? round($totalKaryawanGrup / $totalGrup, 1) : 0 }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-chart-bar text-xs opacity-80"></i>
                        <span>Per Regu Kerja</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        Orang
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. FILTER TOOLBAR (CONSISTENT WITH V2 STANDARDS) ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('grup.index') }}" method="GET" class="w-full">
            <div class="flex flex-col sm:flex-row items-center gap-2.5 sm:gap-3 w-full">
                
                <!-- Search Input -->
                <div class="flex-1 min-w-0 w-full relative">
                    <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none"></i>
                    <input type="text" 
                           name="nama_grup" 
                           value="{{ request('nama_grup') }}" 
                           placeholder="Cari Kode atau Nama Grup..." 
                           class="w-full pl-11 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <!-- Buttons: Cari & Reset -->
                <div class="flex items-center gap-1.5 shrink-0 w-full sm:w-auto">
                    <button type="submit" 
                            class="w-full sm:w-auto px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-search text-sm"></i>
                        <span>Cari</span>
                    </button>
                    @if(request('nama_grup'))
                        <a href="{{ route('grup.index') }}" 
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
                Menampilkan <strong>{{ $grup->count() }}</strong> grup
                <span class="text-slate-300 mx-1.5">|</span>
                Total Anggota: <strong class="text-emerald-700 font-bold">{{ $totalKaryawanGrup }}</strong> pegawai
            </div>

            <!-- View Switcher: Kartu Grid vs Tabel -->
            <div class="flex items-center bg-slate-100 p-0.5 rounded-lg border border-slate-200/80">
                <button type="button" 
                        @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'bg-white text-slate-800 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800'"
                        class="flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] transition cursor-pointer">
                    <i class="ti ti-layout-grid text-sm"></i>
                    <span>Kartu</span>
                </button>
                <button type="button" 
                        @click="viewMode = 'table'"
                        :class="viewMode === 'table' ? 'bg-white text-slate-800 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800'"
                        class="flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] transition cursor-pointer">
                    <i class="ti ti-table text-sm"></i>
                    <span>Tabel</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ================= 4. GRUP LIST: GRID CARDS VIEW ================= -->
    <div x-show="viewMode === 'list'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($grup as $g)
            <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs hover:shadow-md hover:border-emerald-300/80 transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <!-- Top Card Header: Code Badge & Actions -->
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider text-white shadow-2xs"
                                  style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                {{ $g->kode_grup }}
                            </span>
                            <span class="text-[11px] font-bold text-slate-400 font-mono">
                                #{{ $loop->iteration }}
                            </span>
                        </div>

                            <!-- Action Buttons (Edit, Detail, Set Jam Kerja, Delete) -->
                        <div class="flex items-center gap-1">
                            @can('grup.edit')
                                <button type="button" 
                                        class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 border border-blue-200 flex items-center justify-center transition active:scale-95 btnEdit cursor-pointer shadow-2xs"
                                        kode_grup="{{ Crypt::encrypt($g->kode_grup) }}" 
                                        title="Edit Grup">
                                    <i class="ti ti-edit text-xs"></i>
                                </button>
                            @endcan

                            @can('grup.detail')
                                <a href="{{ route('grup.detail', Crypt::encrypt($g->kode_grup)) }}" 
                                   class="w-7 h-7 rounded-lg bg-slate-50 hover:bg-sky-50 text-slate-600 hover:text-sky-700 border border-slate-200 hover:border-sky-200 flex items-center justify-center transition active:scale-95 shadow-2xs"
                                   title="Kelola Anggota Tim">
                                    <i class="ti ti-users text-xs"></i>
                                </a>
                            @endcan

                            @can('grup.setJamKerja')
                                <a href="{{ route('grup.setJamKerja', Crypt::encrypt($g->kode_grup)) }}" 
                                   class="w-7 h-7 rounded-lg bg-slate-50 hover:bg-amber-50 text-slate-600 hover:text-amber-700 border border-slate-200 hover:border-amber-200 flex items-center justify-center transition active:scale-95 shadow-2xs"
                                   title="Atur Jadwal Kerja Grup">
                                    <i class="ti ti-calendar-time text-xs"></i>
                                </a>
                            @endcan

                            @can('grup.delete')
                                <form action="{{ route('grup.delete', Crypt::encrypt($g->kode_grup)) }}" 
                                      method="POST" 
                                      class="delete-grup-form inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-7 h-7 rounded-lg bg-slate-50 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 hover:border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs" 
                                            title="Hapus Grup">
                                        <i class="ti ti-trash text-xs"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>

                    <!-- Group Name & Icon -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200/80 group-hover:border-emerald-200 group-hover:bg-emerald-50/50 flex items-center justify-center text-slate-600 group-hover:text-emerald-700 transition shrink-0 text-lg">
                            <i class="ti ti-users-group"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-sm font-bold text-slate-900 group-hover:text-emerald-800 transition truncate" title="{{ $g->nama_grup }}">
                                {{ $g->nama_grup }}
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Footer: Member Count & Quick Schedule Action -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5 text-slate-600 font-medium">
                        <i class="ti ti-users text-slate-400 text-sm"></i>
                        <span class="font-bold text-slate-800">{{ $g->karyawan_count ?? 0 }}</span>
                        <span class="text-slate-500 text-[11px]">Anggota</span>
                    </div>

                    <a href="{{ route('grup.detail', Crypt::encrypt($g->kode_grup)) }}" 
                       class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                        <span>Kelola Anggota</span>
                        <i class="ti ti-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200 shadow-2xs">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-3">
                    <i class="ti ti-users-off"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Tidak ada data grup ditemukan</h4>
                <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian lain atau tambahkan grup baru.</p>
            </div>
        @endforelse
    </div>

    <!-- ================= 5. GRUP LIST: SEAMLESS SOLID TABLE VIEW ================= -->
    <div x-show="viewMode === 'table'" class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden" style="display: none;">
        
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-users-group"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Data Grup Kerja</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $totalGrup }} data
            </span>
        </div>

        <!-- Responsive Table Container with Compact Balanced Padding -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                <!-- Matching Solid Table Header -->
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-3.5 w-28 text-white/90 border-0 border-t-0">Kode</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Nama Grup</th>
                        <th class="py-2.5 px-3.5 text-center w-36 text-white/90 border-0 border-t-0">Jumlah Anggota</th>
                        <th class="py-2.5 px-3.5 text-center w-36 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($grup as $g)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- No Index -->
                            <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold text-xs">
                                <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <!-- Kode Grup -->
                            <td class="py-2.5 px-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200/80 font-mono text-[11.5px]">
                                    <i class="ti ti-barcode text-slate-400"></i>
                                    {{ $g->kode_grup }}
                                </span>
                            </td>

                            <!-- Nama Grup -->
                            <td class="py-2.5 px-4">
                                <span class="font-bold text-slate-900 block text-xs sm:text-sm">{{ $g->nama_grup }}</span>
                            </td>

                            <!-- Jumlah Anggota -->
                            <td class="py-2.5 px-3.5 text-center">
                                <a href="{{ route('grup.detail', Crypt::encrypt($g->kode_grup)) }}" 
                                   class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold border border-emerald-200 text-xs transition"
                                   title="Kelola anggota grup ini">
                                    <i class="ti ti-users text-sm"></i>
                                    <span>{{ $g->karyawan_count ?? 0 }} Anggota</span>
                                </a>
                            </td>

                            <!-- Aksi -->
                            <td class="py-2.5 px-3.5 text-center">
                                <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                    @can('grup.edit')
                                        <button type="button" 
                                                class="w-7 h-7 rounded-md hover:bg-white text-blue-600 hover:text-blue-700 flex items-center justify-center transition-all cursor-pointer btnEdit"
                                                kode_grup="{{ Crypt::encrypt($g->kode_grup) }}" 
                                                title="Edit Grup">
                                            <i class="ti ti-edit text-sm"></i>
                                        </button>
                                    @endcan

                                    @can('grup.detail')
                                        <a href="{{ route('grup.detail', Crypt::encrypt($g->kode_grup)) }}" 
                                           class="w-7 h-7 rounded-md hover:bg-sky-50 text-sky-600 hover:text-sky-700 flex items-center justify-center transition-all cursor-pointer"
                                           title="Kelola Anggota Tim">
                                            <i class="ti ti-users text-sm"></i>
                                        </a>
                                    @endcan

                                    @can('grup.setJamKerja')
                                        <a href="{{ route('grup.setJamKerja', Crypt::encrypt($g->kode_grup)) }}" 
                                           class="w-7 h-7 rounded-md hover:bg-amber-50 text-amber-600 hover:text-amber-700 flex items-center justify-center transition-all cursor-pointer"
                                           title="Atur Jadwal Kerja Grup">
                                            <i class="ti ti-calendar-time text-sm"></i>
                                        </a>
                                    @endcan

                                    @can('grup.delete')
                                        <form action="{{ route('grup.delete', Crypt::encrypt($g->kode_grup)) }}" 
                                              method="POST" 
                                              class="delete-grup-form inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-7 h-7 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition-all cursor-pointer" 
                                                    title="Hapus Grup">
                                                <i class="ti ti-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 px-4 text-slate-400">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-lg mb-2">
                                    <i class="ti ti-users-off"></i>
                                </div>
                                <p class="font-bold text-slate-700 text-xs">Tidak ada data grup ditemukan</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Coba sesuaikan kata kunci pencarian Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Universal V2 Modal for Create & Edit -->
<div class="modal fade" id="modalV2Grup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between text-white border-0"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs shrink-0">
                        <i class="ti ti-users-group"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm font-extrabold text-white tracking-tight truncate mb-0" id="modalV2GrupTitle">Tambah Grup</h5>
                    </div>
                </div>
                <button type="button" class="text-white/80 hover:text-white transition p-1 rounded-lg hover:bg-white/10 cursor-pointer" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6 bg-white" id="loadmodalV2Grup">
                <!-- Dynamic form loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        const modalEl = document.getElementById('modalV2Grup');
        const modalInstance = new bootstrap.Modal(modalEl);

        function showLoadingSpinner() {
            $('#loadmodalV2Grup').html(`
                <div class="py-10 flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mb-3"></div>
                    <p class="text-xs text-slate-500 font-medium">Memuat form data...</p>
                </div>
            `);
        }

        // 1. Create Grup
        $('#btnCreate').click(function(e) {
            e.preventDefault();
            $('#modalV2GrupTitle').text('Tambah Data Grup');
            modalInstance.show();
            showLoadingSpinner();
            $('#loadmodalV2Grup').load("{{ route('grup.create') }}");
        });

        // 2. Edit Grup
        $('.btnEdit').click(function(e) {
            e.preventDefault();
            const kode_grup = $(this).attr('kode_grup');
            $('#modalV2GrupTitle').text('Edit Data Grup');
            modalInstance.show();
            showLoadingSpinner();
            $('#loadmodalV2Grup').load(`/grup/${kode_grup}`);
        });

        // 3. Delete Confirmation (SweetAlert2)
        $('.delete-grup-form').submit(function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Grup?',
                text: 'Grup ini akan dihapus dari sistem. Pastikan tidak ada data presensi/jadwal yang terikat.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
