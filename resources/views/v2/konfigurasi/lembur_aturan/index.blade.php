@extends('v2.layouts.app')

@section('titlepage', 'Aturan Lembur')
@section('page_title', 'Konfigurasi Aturan Lembur')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalKerja = $aturan_kerja->count();
    $totalLibur = $aturan_libur->count();
    $totalKhusus = $lembur_khusus->count();
@endphp

<div class="space-y-5" x-data="{ activeTab: '{{ request('nama_karyawan_search') ? 'khusus' : 'kerja' }}' }">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMB) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-clock-play text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Aturan Lembur</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola faktor pengali upah lembur berjenjang (tier) dan tarif lembur khusus karyawan
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Action -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-settings text-sm"></i>
                    <span>Konfigurasi</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Aturan Lembur</span>
            </nav>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-gradient-to-r from-emerald-50/70 via-teal-50/40 to-slate-50 border border-emerald-200/70 rounded-2xl p-4 flex items-start gap-3.5 shadow-2xs">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 text-emerald-700 bg-white shadow-xs border border-emerald-100">
            <i class="ti ti-bulb text-xl"></i>
        </div>
        <div class="flex-1 min-w-0 text-xs text-slate-600 leading-relaxed">
            <span class="font-bold text-slate-800 block mb-0.5">Cara Kerja Perhitungan Jam Netto</span>
            Aturan berjenjang menentukan faktor pengali untuk menghitung <strong class="text-emerald-800 font-bold">"Jam Netto"</strong> lembur.
            Contoh di hari kerja: Jam ke-1 (×1.5) dan jam berikutnya (×2.0). Total lembur 2 jam = <strong class="text-emerald-800 font-bold">(1×1.5) + (1×2.0) = 3.5 jam netto</strong>.
        </div>
    </div>

    <!-- ================= 2. TABS SWITCHER & CONTROLS ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <!-- Tabs Buttons -->
        <div class="inline-flex p-1 bg-slate-100 rounded-xl gap-1 self-start">
            <button type="button" 
                    @click="activeTab = 'kerja'" 
                    :class="activeTab === 'kerja' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs transition cursor-pointer">
                <i class="ti ti-briefcase text-sm" :class="activeTab === 'kerja' ? 'text-emerald-600' : ''"></i>
                <span>Hari Kerja</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-md bg-slate-100 text-slate-600 font-semibold">{{ $totalKerja }}</span>
            </button>

            <button type="button" 
                    @click="activeTab = 'libur'" 
                    :class="activeTab === 'libur' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs transition cursor-pointer">
                <i class="ti ti-calendar-off text-sm" :class="activeTab === 'libur' ? 'text-amber-600' : ''"></i>
                <span>Hari Libur</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-md bg-slate-100 text-slate-600 font-semibold">{{ $totalLibur }}</span>
            </button>

            <button type="button" 
                    @click="activeTab = 'khusus'" 
                    :class="activeTab === 'khusus' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs transition cursor-pointer">
                <i class="ti ti-users text-sm" :class="activeTab === 'khusus' ? 'text-slate-900' : ''"></i>
                <span>Lembur Khusus</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-md bg-slate-100 text-slate-600 font-semibold">{{ $totalKhusus }}</span>
            </button>
        </div>

        <!-- Tab Action / Filter Buttons -->
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <!-- Action Tab 1: Hari Kerja -->
            <button type="button" x-show="activeTab === 'kerja'"
                    class="btnCreate inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95 w-full sm:w-auto justify-center"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});"
                    data-tipe="1">
                <i class="ti ti-plus text-sm"></i>
                <span>Tambah Aturan Kerja</span>
            </button>

            <!-- Action Tab 2: Hari Libur -->
            <button type="button" x-show="activeTab === 'libur'"
                    class="btnCreate inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95 bg-gradient-to-r from-amber-500 to-orange-600 w-full sm:w-auto justify-center"
                    data-tipe="2">
                <i class="ti ti-plus text-sm"></i>
                <span>Tambah Aturan Libur</span>
            </button>

            <!-- Action Tab 3: Lembur Khusus (Search & Add) -->
            <div x-show="activeTab === 'khusus'" class="flex items-center gap-2 w-full sm:w-auto">
                <form action="{{ route('lemburaturan.index') }}" method="GET" class="flex items-center gap-1.5 flex-1 sm:w-72">
                    <div class="relative flex-1">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-search text-base"></i>
                        </span>
                        <input type="text" name="nama_karyawan_search" value="{{ request('nama_karyawan_search') }}"
                               placeholder="Cari Nama / NIK Karyawan..."
                               class="w-full h-10 pl-10 pr-3.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                    </div>
                    <button type="submit" 
                            class="h-10 px-3.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap shrink-0"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-search text-xs"></i>
                        <span>Cari</span>
                    </button>
                    @if(request('nama_karyawan_search'))
                        <a href="{{ route('lemburaturan.index') }}" 
                           class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center justify-center border border-slate-200 shrink-0"
                           title="Reset Pencarian">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </form>

                <button type="button" 
                        class="btnCreateKhusus h-10 inline-flex items-center gap-1.5 px-4 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95 shrink-0"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-user-plus text-sm"></i>
                    <span>Tambah Khusus</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ================= 3. TAB CONTENT 1: HARI KERJA (SEAMLESS SOLID TABLE) ================= -->
    <div x-show="activeTab === 'kerja'" class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-briefcase"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Aturan Lembur Hari Kerja (Senin - Sabtu)</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $totalKerja }} Aturan
            </span>
        </div>

        <!-- Table Container with Consistent Styling -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 w-48 text-white/90 border-0 border-t-0">Rentang Jam (Tier)</th>
                        <th class="py-2.5 px-4 text-center w-40 text-white/90 border-0 border-t-0">Faktor Pengali</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Keterangan Jam</th>
                        <th class="py-2.5 px-3.5 text-center w-28 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($aturan_kerja as $idx => $d)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- No Index -->
                            <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold text-xs">
                                <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <!-- Rentang Jam -->
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200/80 font-mono text-[11.5px]">
                                    <i class="ti ti-clock text-slate-400"></i>
                                    Jam {{ number_format($d->jam_dari, 1) }} - {{ $d->jam_sampai < 99 && $d->jam_sampai > 0 ? number_format($d->jam_sampai, 1) : 'Seterusnya' }}
                                </span>
                            </td>

                            <!-- Multiplier Badge -->
                            <td class="py-2.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                                    {{ $d->faktor }}×
                                </span>
                            </td>

                            <!-- Keterangan -->
                            <td class="py-2.5 px-4 text-slate-600 text-xs">
                                @if($d->jam_sampai < 99 && $d->jam_sampai > 0)
                                    Durasi {{ number_format($d->jam_sampai - $d->jam_dari, 1) }} jam pertama
                                @else
                                    Untuk jam lembur ke-{{ number_format($d->jam_dari, 1) }} dan seterusnya
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-2.5 px-3.5 text-center">
                                <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                    <button type="button" 
                                            class="w-7 h-7 rounded-md hover:bg-white text-blue-600 hover:text-blue-700 flex items-center justify-center transition cursor-pointer btnEdit" 
                                            data-id="{{ $d->id }}" 
                                            title="Edit Aturan">
                                        <i class="ti ti-pencil text-sm"></i>
                                    </button>
                                    <form method="POST" class="delete-form inline m-0" action="{{ route('lemburaturan.delete', $d->id) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                class="w-7 h-7 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition cursor-pointer" 
                                                title="Hapus Aturan">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 px-4 text-slate-400 bg-white">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-lg mb-2">
                                    <i class="ti ti-briefcase-off"></i>
                                </div>
                                <p class="font-bold text-slate-700 text-xs">Belum ada aturan lembur hari kerja</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol "Tambah Aturan Kerja" untuk menambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= 4. TAB CONTENT 2: HARI LIBUR (SEAMLESS SOLID TABLE) ================= -->
    <div x-show="activeTab === 'libur'" class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: #ea580c;">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-calendar-off"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Aturan Lembur Hari Libur (Minggu & Nasional)</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $totalLibur }} Aturan
            </span>
        </div>

        <!-- Table Container with Consistent Styling -->
        <div class="overflow-x-auto w-full" style="background-color: #ea580c;">
            <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: #ea580c;">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 w-48 text-white/90 border-0 border-t-0">Rentang Jam (Tier)</th>
                        <th class="py-2.5 px-4 text-center w-40 text-white/90 border-0 border-t-0">Faktor Pengali</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Keterangan Jam</th>
                        <th class="py-2.5 px-3.5 text-center w-28 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($aturan_libur as $idx => $d)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- No Index -->
                            <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold text-xs">
                                <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-amber-50 group-hover:text-amber-700 group-hover:border group-hover:border-amber-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <!-- Rentang Jam -->
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200/80 font-mono text-[11.5px]">
                                    <i class="ti ti-clock text-slate-400"></i>
                                    Jam {{ number_format($d->jam_dari, 1) }} - {{ $d->jam_sampai < 99 && $d->jam_sampai > 0 ? number_format($d->jam_sampai, 1) : 'Seterusnya' }}
                                </span>
                            </td>

                            <!-- Multiplier Badge -->
                            <td class="py-2.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs">
                                    {{ $d->faktor }}×
                                </span>
                            </td>

                            <!-- Keterangan -->
                            <td class="py-2.5 px-4 text-slate-600 text-xs">
                                @if($d->jam_sampai < 99 && $d->jam_sampai > 0)
                                    Durasi {{ number_format($d->jam_sampai - $d->jam_dari, 1) }} jam pertama di hari libur
                                @else
                                    Untuk jam lembur ke-{{ number_format($d->jam_dari, 1) }} dan seterusnya
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-2.5 px-3.5 text-center">
                                <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                    <button type="button" 
                                            class="w-7 h-7 rounded-md hover:bg-white text-blue-600 hover:text-blue-700 flex items-center justify-center transition cursor-pointer btnEdit" 
                                            data-id="{{ $d->id }}" 
                                            title="Edit Aturan">
                                        <i class="ti ti-pencil text-sm"></i>
                                    </button>
                                    <form method="POST" class="delete-form inline m-0" action="{{ route('lemburaturan.delete', $d->id) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                class="w-7 h-7 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition cursor-pointer" 
                                                title="Hapus Aturan">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 px-4 text-slate-400 bg-white">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-lg mb-2">
                                    <i class="ti ti-calendar-off"></i>
                                </div>
                                <p class="font-bold text-slate-700 text-xs">Belum ada aturan lembur hari libur</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol "Tambah Aturan Libur" untuk menambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= 5. TAB CONTENT 3: LEMBUR KHUSUS PEGAWAI (SEAMLESS SOLID TABLE) ================= -->
    <div x-show="activeTab === 'khusus'" class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: #1e293b;">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-users"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Tarif Lembur Khusus Pegawai</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $totalKhusus }} Karyawan
            </span>
        </div>

        <!-- Table Container with Consistent Styling -->
        <div class="overflow-x-auto w-full" style="background-color: #1e293b;">
            <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: #1e293b;">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Karyawan</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Jabatan</th>
                        <th class="py-2.5 px-4 text-center w-40 text-white/90 border-0 border-t-0">Tarif Lembur / Jam</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Keterangan</th>
                        <th class="py-2.5 px-3.5 text-center w-28 text-white/90 border-0 border-t-0">Status</th>
                        <th class="py-2.5 px-3.5 text-center w-28 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($lembur_khusus as $idx => $d)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- No Index -->
                            <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold text-xs">
                                <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-slate-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <!-- Karyawan Info -->
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0 border border-slate-200">
                                        {{ strtoupper(substr($d->nama_karyawan, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block text-xs">{{ $d->nama_karyawan }}</span>
                                        <span class="text-[11px] text-slate-400 font-mono">{{ $d->nik }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Jabatan -->
                            <td class="py-2.5 px-4 text-slate-600 text-xs">{{ $d->nama_jabatan }}</td>

                            <!-- Tarif Upah / Jam -->
                            <td class="py-2.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs font-mono">
                                    Rp {{ number_format($d->upah_perjam, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Keterangan -->
                            <td class="py-2.5 px-4 text-slate-500 text-xs max-w-xs truncate">{{ $d->keterangan ?: '-' }}</td>

                            <!-- Status -->
                            <td class="py-2.5 px-3.5 text-center">
                                @if($d->status == 1)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs">
                                        Non-Aktif
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-2.5 px-3.5 text-center">
                                <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                    <button type="button" 
                                            class="w-7 h-7 rounded-md hover:bg-white text-blue-600 hover:text-blue-700 flex items-center justify-center transition cursor-pointer btnEditKhusus" 
                                            data-id="{{ $d->id }}" 
                                            title="Edit Lembur Khusus">
                                        <i class="ti ti-pencil text-sm"></i>
                                    </button>
                                    <form method="POST" class="delete-form inline m-0" action="{{ \Illuminate\Support\Facades\Route::has('lemburaturan.deletekhusus') ? route('lemburaturan.deletekhusus', $d->id) : '#' }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                class="w-7 h-7 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition cursor-pointer" 
                                                title="Hapus Lembur Khusus">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 px-4 text-slate-400 bg-white">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-lg mb-2">
                                    <i class="ti ti-users-off"></i>
                                </div>
                                <p class="font-bold text-slate-700 text-xs">Belum ada karyawan dengan tarif lembur khusus</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Tarif khusus mengabaikan formula pengali standar dan menggunakan tarif per-jam tetap.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= 6. SIMULASI PERHITUNGAN INTERAKTIF ================= -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-slate-200/70 text-slate-700 flex items-center justify-center shrink-0">
                <i class="ti ti-calculator text-base"></i>
            </div>
            <div>
                <h6 class="text-xs font-black text-slate-900 uppercase tracking-wider">Simulasi Formula Perhitungan Jam Netto</h6>
                <p class="text-[11px] text-slate-500 font-medium">Contoh implementasi aturan berjenjang aktif di sistem</p>
            </div>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Simulasi Hari Kerja -->
            <div class="bg-slate-50/70 rounded-xl p-4 border border-slate-200/70 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <i class="ti ti-briefcase text-emerald-600 text-base"></i>
                        <span class="font-bold text-xs text-slate-800">Contoh: Lembur 3 Jam di Hari Kerja</span>
                    </div>

                    <div class="space-y-1.5 text-xs text-slate-600 font-mono">
                        @if($aturan_kerja->count() > 0)
                            @php
                                $contoh_jam = 3;
                                $total_netto = 0;
                                $detail_parts = [];
                                foreach($aturan_kerja as $rule) {
                                    $start = $rule->jam_dari; 
                                    $end = $rule->jam_sampai ?: 99;
                                    
                                    $jam_di_tier_ini = max(0, min($contoh_jam, $end) - $start);
                                    
                                    if ($jam_di_tier_ini > 0) {
                                        $netto = $jam_di_tier_ini * $rule->faktor;
                                        $total_netto += $netto;
                                        $detail_parts[] = 'Jam ' . number_format($start, 1) . '-' . number_format($end, 1) . ' (' . number_format($jam_di_tier_ini, 1) . ' jam) × ' . $rule->faktor . ' = ' . number_format($netto, 1) . ' jam';
                                    }
                                }
                            @endphp
                            @foreach($detail_parts as $part)
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>{{ $part }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-slate-400 font-sans italic text-xs">Tambahkan aturan hari kerja untuk melihat simulasi.</p>
                        @endif
                    </div>
                </div>

                @if($aturan_kerja->count() > 0)
                    <div class="mt-4 pt-3 border-t border-dashed border-slate-200 flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-600">Total Jam Netto Dibayarkan:</span>
                        <span class="font-black text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 shadow-2xs">
                            {{ number_format($total_netto, 1) }} Jam Netto
                        </span>
                    </div>
                @endif
            </div>

            <!-- Simulasi Hari Libur -->
            <div class="bg-slate-50/70 rounded-xl p-4 border border-slate-200/70 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <i class="ti ti-calendar-off text-amber-600 text-base"></i>
                        <span class="font-bold text-xs text-slate-800">Contoh: Lembur 9 Jam di Hari Libur</span>
                    </div>

                    <div class="space-y-1.5 text-xs text-slate-600 font-mono">
                        @if($aturan_libur->count() > 0)
                            @php
                                $contoh_jam = 9;
                                $total_netto = 0;
                                $detail_parts = [];
                                foreach($aturan_libur as $rule) {
                                    $start = $rule->jam_dari; 
                                    $end = $rule->jam_sampai ?: 99;
                                    
                                    $jam_di_tier_ini = max(0, min($contoh_jam, $end) - $start);
                                    
                                    if ($jam_di_tier_ini > 0) {
                                        $netto = $jam_di_tier_ini * $rule->faktor;
                                        $total_netto += $netto;
                                        $detail_parts[] = 'Jam ' . number_format($start, 1) . '-' . number_format($end, 1) . ' (' . number_format($jam_di_tier_ini, 1) . ' jam) × ' . $rule->faktor . ' = ' . number_format($netto, 1) . ' jam';
                                    }
                                }
                            @endphp
                            @foreach($detail_parts as $part)
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>{{ $part }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-slate-400 font-sans italic text-xs">Tambahkan aturan hari libur untuk melihat simulasi.</p>
                        @endif
                    </div>
                </div>

                @if($aturan_libur->count() > 0)
                    <div class="mt-4 pt-3 border-t border-dashed border-slate-200 flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-600">Total Jam Netto Dibayarkan:</span>
                        <span class="font-black text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 shadow-2xs">
                            {{ number_format($total_netto, 1) }} Jam Netto
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Universal V2 Modal for Overtime Rules -->
<div class="modal fade" id="modalV2Lembur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between text-white border-0"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs shrink-0">
                        <i class="ti ti-clock-play" id="modalV2Icon"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm font-extrabold text-white tracking-tight truncate mb-0" id="modalV2Title">Form Aturan Lembur</h5>
                    </div>
                </div>
                <button type="button" class="text-white/80 hover:text-white transition p-1 rounded-lg hover:bg-white/10 cursor-pointer" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6 bg-white" id="loadmodalV2Lembur">
                <!-- Dynamic form loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        const modalEl = document.getElementById('modalV2Lembur');
        const modalInstance = new bootstrap.Modal(modalEl);

        function showLoadingSpinner() {
            $('#loadmodalV2Lembur').html(`
                <div class="py-10 flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mb-3"></div>
                    <p class="text-xs text-slate-500 font-medium">Memuat form data...</p>
                </div>
            `);
        }

        // 1. Tambah Aturan Hari Kerja / Hari Libur
        $('.btnCreate').click(function(e) {
            e.preventDefault();
            const tipe = $(this).data('tipe');
            const tipeLabel = tipe == 1 ? 'Hari Kerja' : 'Hari Libur';
            $('#modalV2Title').text('Tambah Aturan Lembur (' + tipeLabel + ')');
            $('#modalV2Subtitle').text('Tentukan rentang jam dan faktor pengali lembur');
            modalInstance.show();
            showLoadingSpinner();
            $('#loadmodalV2Lembur').load('/lemburaturan/create?tipe_hari=' + tipe);
        });

        // 2. Edit Aturan Hari Kerja / Hari Libur
        $('.btnEdit').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#modalV2Title').text('Edit Aturan Lembur');
            $('#modalV2Subtitle').text('Ubah rentang jam atau faktor pengali lembur');
            modalInstance.show();
            showLoadingSpinner();
            $('#loadmodalV2Lembur').load('/lemburaturan/edit?id=' + id);
        });

        // 3. Tambah Lembur Khusus Karyawan
        $('.btnCreateKhusus').click(function(e) {
            e.preventDefault();
            $('#modalV2Title').text('Tambah Lembur Khusus Pegawai');
            $('#modalV2Subtitle').text('Tetapkan tarif lembur flat per jam untuk karyawan tertentu');
            modalInstance.show();
            showLoadingSpinner();
            $('#loadmodalV2Lembur').load('/lemburaturan/createkhusus');
        });

        // 4. Edit Lembur Khusus Karyawan
        $('.btnEditKhusus').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#modalV2Title').text('Edit Lembur Khusus Pegawai');
            $('#modalV2Subtitle').text('Perbarui tarif flat atau status lembur khusus karyawan');
            modalInstance.show();
            showLoadingSpinner();
            $('#loadmodalV2Lembur').load('/lemburaturan/editkhusus?id=' + id);
        });

        // 5. Delete Confirmation (SweetAlert2)
        $('.delete-form').submit(function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Data?',
                text: 'Data yang dihapus tidak dapat dipulihkan kembali.',
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
