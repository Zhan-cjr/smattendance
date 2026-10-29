@extends('v2.layouts.app')

@section('titlepage', 'Jabatan')
@section('page_title', 'Data Jabatan')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalJabatan = $jabatan->count();
    $totalKaryawan = $jabatan->sum('karyawan_count');
@endphp

<div class="space-y-5" x-data="{ viewMode: 'list' }">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-briefcase text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Jabatan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola jenjang karier, posisi struktural & fungsional karyawan organisasi
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
                <span class="font-bold text-slate-800">Jabatan</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('jabatan.create')
                    <!-- Tambah Jabatan -->
                    <button type="button" id="btnCreate" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Jabatan</span>
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
            <i class="ti ti-briefcase text-[190px]"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            <!-- Segment 1: Total Jabatan -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Jabatan
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Posisi
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalJabatan) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-id-badge-2 text-xs opacity-80"></i>
                        <span>Struktur Organisasi</span>
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
                        <span>Total Personel</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        100%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. FILTER TOOLBAR & VIEW TOGGLE (CONSISTENT WITH V2 STANDARD) ================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs">
        <form method="GET" action="{{ route('jabatan.index') }}" id="filterJabatanForm">
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                
                <!-- Search Box -->
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" 
                           name="nama_jabatan" 
                           id="searchJabatan"
                           value="{{ request('nama_jabatan') }}" 
                           placeholder="Cari kode atau nama jabatan..." 
                           class="w-full h-10 pl-10 pr-10 text-xs sm:text-sm font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                    
                    @if(request('nama_jabatan'))
                        <a href="{{ route('jabatan.index') }}" 
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

                    @if(request('nama_jabatan'))
                        <a href="{{ route('jabatan.index') }}" 
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
                        <i class="ti ti-briefcase text-lg"></i>
                        <span class="text-xs sm:text-sm font-extrabold tracking-wide">Daftar Jabatan</span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-white/20 text-white border border-white/30 backdrop-blur-xs">
                        {{ $jabatan->count() }} Data
                    </span>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-white border-b border-white/20 text-[11px] font-black uppercase tracking-wider"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <th class="py-2.5 px-4 text-center w-14">NO</th>
                                <th class="py-2.5 px-4 w-32">KODE</th>
                                <th class="py-2.5 px-4">NAMA JABATAN</th>
                                <th class="py-2.5 px-4 text-center w-36">JML KARYAWAN</th>
                                <th class="py-2.5 px-4 text-center w-28">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700 bg-white">
                            @forelse ($jabatan as $j)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <!-- NO -->
                                    <td class="py-2 px-4 text-center font-bold text-slate-400">
                                        {{ $loop->iteration }}
                                    </td>

                                    <!-- KODE JABATAN -->
                                    <td class="py-2 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-black tracking-wider bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $j->kode_jabatan }}
                                        </span>
                                    </td>

                                    <!-- NAMA JABATAN -->
                                    <td class="py-2 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0 shadow-2xs"
                                                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                                {{ strtoupper(substr($j->nama_jabatan, 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-emerald-700 transition-colors">
                                                    {{ $j->nama_jabatan }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- JUMLAH KARYAWAN -->
                                    <td class="py-2 px-4 text-center">
                                        @if(($j->karyawan_count ?? 0) > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="ti ti-users text-xs"></i>
                                                <span>{{ $j->karyawan_count }} Karyawan</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-400 border border-slate-200">
                                                <i class="ti ti-user-x text-xs"></i>
                                                <span>Kosong</span>
                                            </span>
                                        @endif
                                    </td>

                                    <!-- AKSI -->
                                    <td class="py-2 px-4 text-center">
                                        <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                            @can('jabatan.edit')
                                                <button type="button" 
                                                        class="w-7 h-7 rounded-md hover:bg-white text-blue-600 hover:text-blue-700 flex items-center justify-center transition-all cursor-pointer btnEdit"
                                                        kode_jabatan="{{ Crypt::encrypt($j->kode_jabatan) }}"
                                                        title="Edit Jabatan">
                                                    <i class="ti ti-edit text-sm"></i>
                                                </button>
                                            @endcan

                                            @can('jabatan.delete')
                                                <form action="{{ route('jabatan.delete', Crypt::encrypt($j->kode_jabatan)) }}" 
                                                      method="POST" 
                                                      class="m-0 deleteform">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="w-7 h-7 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition-all cursor-pointer delete-confirm"
                                                            title="Hapus Jabatan">
                                                        <i class="ti ti-trash text-sm"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                                                <i class="ti ti-briefcase-off"></i>
                                            </div>
                                            <p class="text-xs font-bold text-slate-600 mb-0">Tidak ada data jabatan ditemukan</p>
                                            <p class="text-[11px] text-slate-400 mb-0">Coba ubah kata kunci pencarian atau tambahkan jabatan baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- VIEW MODE: GRID CARD -->
        <div x-show="viewMode === 'grid'" x-cloak>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse ($jabatan as $j)
                    <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-slate-300 p-4 sm:p-5 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                        
                        <!-- Card Top: Code & Action Menu -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black text-white shrink-0 shadow-sm"
                                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                    {{ strtoupper(substr($j->nama_jabatan, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $j->kode_jabatan }}
                                    </span>
                                    <h4 class="text-sm font-extrabold text-slate-900 truncate mt-1 group-hover:text-emerald-700 transition">
                                        {{ $j->nama_jabatan }}
                                    </h4>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-1.5 shrink-0">
                                @can('jabatan.edit')
                                    <button type="button" 
                                            class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 border border-blue-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs btnEdit"
                                            kode_jabatan="{{ Crypt::encrypt($j->kode_jabatan) }}"
                                            title="Edit">
                                        <i class="ti ti-edit text-xs"></i>
                                    </button>
                                @endcan

                                @can('jabatan.delete')
                                    <form action="{{ route('jabatan.delete', Crypt::encrypt($j->kode_jabatan)) }}" 
                                          method="POST" 
                                          class="m-0 deleteform">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 border border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs delete-confirm"
                                                title="Hapus">
                                            <i class="ti ti-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>

                        <!-- Card Bottom: Employee Count Badge -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-400 text-[11px] font-medium flex items-center gap-1">
                                <i class="ti ti-users text-xs"></i>
                                <span>Personel</span>
                            </span>
                            @if(($j->karyawan_count ?? 0) > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $j->karyawan_count }} Karyawan
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-400 border border-slate-200">
                                    0 Karyawan
                                </span>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-2xl border border-slate-200">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                                <i class="ti ti-briefcase-off"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-600 mb-0">Tidak ada data jabatan ditemukan</p>
                            <p class="text-[11px] text-slate-400 mb-0">Coba ubah kata kunci pencarian atau tambahkan jabatan baru.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- ================= MODAL CONTAINER ================= -->
<div class="modal fade" id="modalJabatanV2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-briefcase"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalJabatanTitle">Tambah Data Jabatan</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalJabatanSubtitle">Formulir konfigurasi data jabatan karyawan</p>
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
<script>
    $(function() {
        const modalEl = document.getElementById('modalJabatanV2');
        const modalInstance = new bootstrap.Modal(modalEl);

        function showLoading() {
            $("#loadModalContent").html(`
                <div class="py-10 text-center text-slate-400">
                    <div class="w-8 h-8 border-4 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mx-auto mb-2.5"></div>
                    <p class="text-xs font-medium">Memuat formulir...</p>
                </div>
            `);
        }

        // Button Tambah Jabatan
        $("#btnCreate").click(function() {
            $("#modalJabatanTitle").text("Tambah Data Jabatan");
            $("#modalJabatanSubtitle").text("Formulir penambahan jabatan baru dalam organisasi");
            showLoading();
            modalInstance.show();
            $("#loadModalContent").load("{{ route('jabatan.create') }}");
        });

        // Button Edit Jabatan
        $(document).on("click", ".btnEdit", function() {
            const kode_jabatan = $(this).attr("kode_jabatan");
            $("#modalJabatanTitle").text("Edit Data Jabatan");
            $("#modalJabatanSubtitle").text("Perbarui informasi kode atau nama jabatan");
            showLoading();
            modalInstance.show();
            $("#loadModalContent").load(`/jabatan/${kode_jabatan}`);
        });
    });
</script>
@endpush
