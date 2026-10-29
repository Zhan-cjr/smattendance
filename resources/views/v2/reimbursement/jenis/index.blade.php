@extends('v2.layouts.app')

@section('titlepage', 'Jenis Reimbursement')
@section('page_title', 'Jenis Reimbursement')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalJenis = $jenis_reimbursement->count();
    $totalAktif = $jenis_reimbursement->where('status', 1)->count();
    $totalNonaktif = $jenis_reimbursement->where('status', 0)->count();
    $totalWajibBukti = $jenis_reimbursement->where('wajib_bukti', 1)->count();
@endphp

<div class="space-y-5" x-data="{ viewMode: 'list' }">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-receipt-refund text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Jenis & Aturan Reimbursement</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Manajemen jenis klaim pengeluaran, plafon nominal, aturan bukti nota, dan penugasan karyawan
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
                    <i class="ti ti-receipt-2 text-sm"></i>
                    <span>Reimbursement</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Jenis & Aturan</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('jenisreimbursement.create')
                    <button type="button" id="btnCreateJenisV2" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Jenis Reimbursement</span>
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
            <i class="ti ti-receipt-refund text-[190px]"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            
            <!-- Segment 1: Total Master Jenis -->
            <div class="flex-1 min-w-[140px] sm:min-w-[160px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Jenis Klaim
                        </span>
                        <span class="p-1.5 rounded-lg bg-white/10 text-white group-hover:scale-110 transition shrink-0">
                            <i class="ti ti-folders text-xs"></i>
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalJenis) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-list text-xs opacity-80"></i>
                        <span>Kategori Klaim</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        100%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Kategori Aktif -->
            <div class="flex-1 min-w-[140px] sm:min-w-[160px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Status Aktif
                        </span>
                        <span class="p-1.5 rounded-lg bg-white/10 text-emerald-200 group-hover:scale-110 transition shrink-0">
                            <i class="ti ti-circle-check text-xs"></i>
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalAktif) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-check text-xs opacity-80"></i>
                        <span>Dapat Diajukan</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-emerald-400/25 px-2 py-0.5 rounded-md">
                        {{ $totalJenis > 0 ? round(($totalAktif / $totalJenis) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Status Nonaktif -->
            <div class="flex-1 min-w-[140px] sm:min-w-[160px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Nonaktif / Tutup
                        </span>
                        <span class="p-1.5 rounded-lg bg-white/10 text-rose-200 group-hover:scale-110 transition shrink-0">
                            <i class="ti ti-ban text-xs"></i>
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalNonaktif) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-lock text-xs opacity-80"></i>
                        <span>Terkunci</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-rose-400/25 px-2 py-0.5 rounded-md">
                        {{ $totalJenis > 0 ? round(($totalNonaktif / $totalJenis) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 4: Wajib Upload Nota -->
            <div class="flex-1 min-w-[140px] sm:min-w-[160px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Wajib Lampiran
                        </span>
                        <span class="p-1.5 rounded-lg bg-white/10 text-amber-200 group-hover:scale-110 transition shrink-0">
                            <i class="ti ti-file-certificate text-xs"></i>
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalWajibBukti) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-camera text-xs opacity-80"></i>
                        <span>Upload Nota Bukti</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $totalJenis > 0 ? round(($totalWajibBukti / $totalJenis) * 100) : 0 }}%
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= 3. FILTER TOOLBAR (CONSISTENT WITH V2 STANDARDS) ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('jenisreimbursement.index') }}" method="GET" class="w-full" id="filterFormJenisV2">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 sm:gap-3 w-full items-center">
                
                <!-- Search Input (Kode / Nama / Deskripsi) -->
                <div class="sm:col-span-6 relative">
                    <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none"></i>
                    <input type="text" 
                           name="nama_jenis" 
                           id="searchNamaJenisV2"
                           value="{{ request('nama_jenis') }}" 
                           placeholder="Cari Kode atau Nama Jenis Reimbursement..." 
                           class="w-full pl-11 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <!-- Filter Status -->
                <div class="sm:col-span-3">
                    <select name="status" class="w-full py-2.5 px-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-bold shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Status Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Status Nonaktif</option>
                    </select>
                </div>

                <!-- Buttons: Cari & Reset -->
                <div class="sm:col-span-3 flex items-center gap-1.5 w-full">
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-search text-sm"></i>
                        <span>Cari</span>
                    </button>
                    @if(request('nama_jenis') || request('status') !== null || request('wajib_bukti') !== null)
                        <a href="{{ route('jenisreimbursement.index') }}" 
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
                Menampilkan <strong class="text-slate-800">{{ $jenis_reimbursement->count() }}</strong> kategori klaim
                <span class="text-slate-300 mx-1.5">|</span>
                Aktif: <strong class="text-emerald-700 font-bold">{{ $totalAktif }}</strong> kategori
            </div>

            <!-- View Switcher: Tabel vs Grid -->
            <div class="flex items-center bg-slate-100 p-0.5 rounded-lg border border-slate-200/80">
                <button type="button" 
                        @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'bg-white text-slate-800 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800'"
                        class="flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] transition cursor-pointer">
                    <i class="ti ti-table text-sm"></i>
                    <span>Tabel</span>
                </button>
                <button type="button" 
                        @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'bg-white text-slate-800 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800'"
                        class="flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] transition cursor-pointer">
                    <i class="ti ti-layout-grid text-sm"></i>
                    <span>Kartu</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ================= 4. TABLE VIEW CONTAINER ================= -->
    <div x-show="viewMode === 'list'" class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-receipt-refund"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Jenis Reimbursement & Aturan Plafon</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                {{ $totalJenis }} Kategori
            </span>
        </div>

        <!-- Seamless Table -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse whitespace-nowrap" id="tableJenisReimburseV2">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0">#</th>
                        <th class="py-2.5 px-4 text-white/90 border-0">Kode</th>
                        <th class="py-2.5 px-4 text-white/90 border-0">Nama Jenis & Deskripsi</th>
                        <th class="py-2.5 px-4 text-right text-white/90 border-0">Plafon /Klaim</th>
                        <th class="py-2.5 px-4 text-right text-white/90 border-0">Plafon /Bulan</th>
                        <th class="py-2.5 px-4 text-right text-white/90 border-0">Plafon /Tahun</th>
                        <th class="py-2.5 px-4 text-center text-white/90 border-0">Bukti Nota</th>
                        <th class="py-2.5 px-4 text-center text-white/90 border-0">Status</th>
                        <th class="py-2.5 px-4 text-center w-28 text-white/90 border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white" id="bodyJenisReimburseV2">
                    @forelse ($jenis_reimbursement as $index => $d)
                        <tr class="hover:bg-slate-50/80 transition-colors rowJenisReimburse"
                            data-search="{{ strtolower($d->kode_jenis_reimburse . ' ' . $d->nama_jenis . ' ' . $d->deskripsi) }}">
                            
                            <!-- Index -->
                            <td class="py-3 px-3.5 text-center font-bold text-slate-400">
                                <span class="w-6 h-6 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto text-[11px] font-black text-slate-700">
                                    {{ $index + 1 }}
                                </span>
                            </td>

                            <!-- Kode -->
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                <span class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 border border-sky-200/80 text-[11px] font-mono font-black">
                                    {{ $d->kode_jenis_reimburse }}
                                </span>
                            </td>

                            <!-- Nama & Deskripsi -->
                            <td class="py-3 px-4">
                                <span class="text-xs sm:text-sm font-extrabold text-slate-900 block">
                                    {{ $d->nama_jenis }}
                                </span>
                                <span class="text-[11px] text-slate-400 block mt-0.5 truncate max-w-xs">
                                    {{ $d->deskripsi ?: 'Tidak ada keterangan tambahan' }}
                                </span>
                            </td>

                            <!-- Plafon per Klaim -->
                            <td class="py-3 px-4 text-right font-extrabold font-mono text-xs text-slate-800">
                                @if($d->batas_nominal)
                                    <span class="text-emerald-700 font-black">Rp {{ number_format($d->batas_nominal, 0, ',', '.') }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400">
                                        <i class="ti ti-infinity text-xs"></i> Unlimited
                                    </span>
                                @endif
                            </td>

                            <!-- Plafon per Bulan -->
                            <td class="py-3 px-4 text-right font-extrabold font-mono text-xs text-slate-800">
                                @if($d->batas_nominal_bulanan)
                                    <span class="text-sky-700 font-black">Rp {{ number_format($d->batas_nominal_bulanan, 0, ',', '.') }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400">
                                        <i class="ti ti-infinity text-xs"></i> Unlimited
                                    </span>
                                @endif
                            </td>

                            <!-- Plafon per Tahun -->
                            <td class="py-3 px-4 text-right font-extrabold font-mono text-xs text-slate-800">
                                @if($d->batas_nominal_tahunan)
                                    <span class="text-purple-700 font-black">Rp {{ number_format($d->batas_nominal_tahunan, 0, ',', '.') }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400">
                                        <i class="ti ti-infinity text-xs"></i> Unlimited
                                    </span>
                                @endif
                            </td>

                            <!-- Wajib Bukti -->
                            <td class="py-3 px-4 text-center">
                                @if($d->wajib_bukti)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="ti ti-camera text-xs"></i> Wajib Nota
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200">
                                        <i class="ti ti-minus text-xs"></i> Opsional
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4 text-center">
                                @if($d->status)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ti ti-check text-xs"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="ti ti-x text-xs"></i> Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @can('jenisreimbursement.edit')
                                        <!-- Enrolled Karyawan -->
                                        <a href="{{ route('jenisreimbursement.setkaryawan', Crypt::encrypt($d->id)) }}" 
                                           class="p-2 rounded-xl bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                           title="Kelola Karyawan Terdaftar (Enrollment)">
                                            <i class="ti ti-users text-sm"></i>
                                        </a>

                                        <!-- Edit -->
                                        <button type="button" 
                                                class="btnEditJenisV2 p-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                                data-id="{{ Crypt::encrypt($d->id) }}"
                                                title="Edit Parameter Jenis">
                                            <i class="ti ti-edit text-sm"></i>
                                        </button>
                                    @endcan

                                    @can('jenisreimbursement.delete')
                                        <!-- Delete -->
                                        <form action="{{ route('jenisreimbursement.delete', Crypt::encrypt($d->id)) }}" method="POST" class="deleteform m-0 inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btnDeleteJenisV2 p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                                    title="Hapus Jenis Reimbursement">
                                                <i class="ti ti-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-12 text-center text-slate-400 bg-white">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                    <i class="ti ti-receipt-off"></i>
                                </div>
                                <h5 class="text-xs font-bold text-slate-700">Belum Ada Jenis Reimbursement</h5>
                                <p class="text-[11px] text-slate-400 mt-0.5">Silakan tambahkan jenis reimbursement baru melalui tombol di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= 5. GRID VIEW CONTAINER ================= -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="gridJenisReimburseV2">
        @forelse ($jenis_reimbursement as $d)
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md transition-all p-5 flex flex-col justify-between cardGridJenis"
                 data-search="{{ strtolower($d->kode_jenis_reimburse . ' ' . $d->nama_jenis . ' ' . $d->deskripsi) }}">
                <div>
                    <!-- Top header -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center justify-center text-lg shrink-0">
                                <i class="ti ti-receipt-refund"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 border border-sky-200/80 text-[10.5px] font-mono font-black inline-block mb-1">
                                    {{ $d->kode_jenis_reimburse }}
                                </span>
                                <h4 class="text-sm font-black text-slate-900 truncate">
                                    {{ $d->nama_jenis }}
                                </h4>
                            </div>
                        </div>
                        
                        @if($d->status)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                Aktif
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200 shrink-0">
                                Nonaktif
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-500 mt-3 line-clamp-2 leading-relaxed">
                        {{ $d->deskripsi ?: 'Tidak ada deskripsi detail untuk jenis ini.' }}
                    </p>

                    <!-- Plafon Limits Box -->
                    <div class="mt-4 p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-semibold">Plafon /Klaim:</span>
                            <span class="font-extrabold font-mono text-emerald-700">
                                {{ $d->batas_nominal ? 'Rp ' . number_format($d->batas_nominal, 0, ',', '.') : 'Unlimited' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-semibold">Plafon /Bulan:</span>
                            <span class="font-extrabold font-mono text-sky-700">
                                {{ $d->batas_nominal_bulanan ? 'Rp ' . number_format($d->batas_nominal_bulanan, 0, ',', '.') : 'Unlimited' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-semibold">Plafon /Tahun:</span>
                            <span class="font-extrabold font-mono text-purple-700">
                                {{ $d->batas_nominal_tahunan ? 'Rp ' . number_format($d->batas_nominal_tahunan, 0, ',', '.') : 'Unlimited' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center gap-2">
                        @if($d->wajib_bukti)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700">
                                <i class="ti ti-camera text-xs"></i> Wajib Upload Bukti Nota
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400">
                                <i class="ti ti-file-off text-xs"></i> Nota Opsional
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('jenisreimbursement.setkaryawan', Crypt::encrypt($d->id)) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200/80 text-xs font-bold transition active:scale-95 shadow-2xs">
                        <i class="ti ti-users text-xs"></i>
                        <span>Enrollment</span>
                    </a>

                    <div class="flex items-center gap-1.5">
                        @can('jenisreimbursement.edit')
                            <button type="button" 
                                    class="btnEditJenisV2 p-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                    data-id="{{ Crypt::encrypt($d->id) }}"
                                    title="Edit">
                                <i class="ti ti-edit text-xs"></i>
                            </button>
                        @endcan

                        @can('jenisreimbursement.delete')
                            <form action="{{ route('jenisreimbursement.delete', Crypt::encrypt($d->id)) }}" method="POST" class="deleteform m-0 inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btnDeleteJenisV2 p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                        title="Hapus">
                                    <i class="ti ti-trash text-xs"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center bg-white rounded-2xl border border-slate-200/90 shadow-2xs text-slate-400">
                <i class="ti ti-receipt-off text-4xl text-slate-300 mb-2 d-block"></i>
                <p class="text-sm font-bold text-slate-700">Belum ada data jenis reimbursement</p>
            </div>
        @endforelse
    </div>

</div>

<!-- ================= UNIVERSAL MODAL JENIS REIMBURSEMENT (CREATE & EDIT) ================= -->
<div id="modalJenisUniversalV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative shrink-0"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-receipt-refund text-lg" id="modalJenisIcon"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="modalJenisTitle">
                        Jenis Reimbursement
                    </h3>
                    <p class="text-[11px] text-slate-500" id="modalJenisSubtitle">
                        Parameter jenis klaim & aturan plafon
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseModalJenis w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="modalJenisContent">
            <div class="py-12 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                <p class="text-xs font-semibold">Memuat formulir...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        // Search Filter Logic (Table & Grid)
        $('#searchJenisReimburseV2').on('keyup', function() {
            let val = $(this).val().toLowerCase().trim();
            
            // Filter Table
            $('.rowJenisReimburse').each(function() {
                let search = $(this).data('search');
                $(this).toggle(search.indexOf(val) > -1);
            });

            // Filter Grid
            $('.cardGridJenis').each(function() {
                let search = $(this).data('search');
                $(this).toggle(search.indexOf(val) > -1);
            });
        });

        // Modal Handlers
        function showModalJenis(title, subtitle, icon, url) {
            $('#modalJenisTitle').text(title);
            $('#modalJenisSubtitle').text(subtitle);
            $('#modalJenisIcon').attr('class', 'ti ' + icon + ' text-lg');
            $('#modalJenisUniversalV2').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            $('#modalJenisContent').html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat formulir...</p>
                </div>
            `);
            $('#modalJenisContent').load(url, function() {
                if (typeof $.fn.maskMoney !== 'undefined') {
                    $('#modalJenisContent .money').maskMoney({
                        thousands: '.', decimal: ',', precision: 0, allowZero: true
                    });
                }
            });
        }

        function closeModalJenis() {
            $('#modalJenisUniversalV2').addClass('hidden');
            $('body').removeClass('overflow-hidden');
            $('#modalJenisContent').html('');
        }

        $('#btnCreateJenisV2').click(function(e) {
            e.preventDefault();
            showModalJenis('Tambah Jenis Reimbursement', 'Definisikan kategori klaim dan batas nominal plafon', 'ti-plus', '{{ route("jenisreimbursement.create") }}');
        });

        $(document).on('click', '.btnEditJenisV2', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            showModalJenis('Edit Jenis Reimbursement', 'Perbarui aturan batas nominal dan opsi lampiran bukti', 'ti-edit', `/jenisreimbursement/${id}/edit`);
        });

        $(document).on('click', '.btnCloseModalJenis', closeModalJenis);

        // Backdrop Click
        $('#modalJenisUniversalV2').click(function(e) {
            if (e.target === this) closeModalJenis();
        });

        // ESC Key
        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$('#modalJenisUniversalV2').hasClass('hidden')) {
                closeModalJenis();
            }
        });

        // Delete Confirmation
        $(document).on('click', '.btnDeleteJenisV2', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: 'Jenis reimbursement yang dihapus dapat mempengaruhi data master pengajuan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '{{ $themeColor1 }}',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!',
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
