@extends('v2.layouts.app')

@section('titlepage', 'Log Mesin Presensi')
@section('page_title', 'Log Mesin Presensi')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMBS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-device-desktop-analytics text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Log Mesin Presensi</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Monitoring audit trail data log mentah transaksi dari mesin presensi fingerprint & RFID
            </p>
        </div>

        <!-- Breadcrumbs -->
        <nav class="flex items-center text-xs text-slate-400 font-medium self-start md:self-auto">
            <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                <i class="ti ti-home text-sm"></i>
                <span>Dashboard</span>
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="text-slate-500 flex items-center gap-1">
                <i class="ti ti-tool text-sm"></i>
                <span>Utilities</span>
            </span>
            <span class="mx-2 text-slate-300">/</span>
            <span class="font-bold text-slate-800">Log Mesin</span>
        </nav>
    </div>

    <!-- ================= 2. FILTER TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('logmesin.index') }}" method="GET" class="w-full">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 sm:gap-3 w-full items-center">
                <!-- Search Name / PIN -->
                <div class="sm:col-span-5 lg:col-span-5 relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" name="nama_karyawan" id="nama_karyawan"
                           value="{{ request('nama_karyawan') }}"
                           placeholder="Cari nama karyawan atau PIN..."
                           class="w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                </div>

                <!-- Date Range: Tanggal Awal -->
                <div class="sm:col-span-3 lg:col-span-3 relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" name="tanggal_awal" id="tanggal_awal"
                           value="{{ request('tanggal_awal') }}"
                           placeholder="Tanggal Awal"
                           class="flatpickr-date w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                </div>

                <!-- Date Range: Tanggal Akhir -->
                <div class="sm:col-span-3 lg:col-span-2 relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" name="tanggal_akhir" id="tanggal_akhir"
                           value="{{ request('tanggal_akhir') }}"
                           placeholder="Tanggal Akhir"
                           class="flatpickr-date w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                </div>

                <!-- Actions Button -->
                <div class="sm:col-span-1 lg:col-span-2 flex items-center gap-2 w-full justify-end">
                    <button type="submit" 
                            class="flex-1 h-10 px-4 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-search text-sm"></i>
                        <span>Cari</span>
                    </button>
                    @if(request()->filled('nama_karyawan') || request()->filled('tanggal_awal') || request()->filled('tanggal_akhir'))
                        <a href="{{ route('logmesin.index') }}" 
                           class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center justify-center border border-slate-200"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- ================= 3. SEAMLESS SOLID TABLE ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-device-floppy"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Riwayat Log Mesin Presensi
                    </h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $logs->total() }} Total Log
            </span>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-14 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-3.5 text-center w-24 text-white/90 border-0 border-t-0">PIN</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[200px]">Karyawan</th>
                        <th class="py-2.5 px-3.5 text-center w-36 text-white/90 border-0 border-t-0">Jam Absen</th>
                        <th class="py-2.5 px-3.5 text-center w-32 text-white/90 border-0 border-t-0">Scan Type</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[150px]">Mesin</th>
                        <th class="py-2.5 px-3.5 text-center w-28 text-white/90 border-0 border-t-0">Status</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[180px]">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($logs as $idx => $log)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- No -->
                        <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold">
                            {{ $logs->firstItem() + $idx }}
                        </td>

                        <!-- PIN -->
                        <td class="py-2.5 px-3.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[11px] font-black bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $log->pin }}
                            </span>
                        </td>

                        <!-- Karyawan -->
                        <td class="py-2.5 px-4">
                            @if($log->nama_karyawan)
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs text-white shrink-0 shadow-2xs"
                                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                        {{ strtoupper(substr($log->nama_karyawan, 0, 1)) }}
                                    </div>
                                    <span class="font-extrabold text-slate-800 text-xs">
                                        {{ $log->nama_karyawan }}
                                    </span>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-rose-500 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100 italic">
                                    <i class="ti ti-user-x text-xs"></i> Tidak ditemukan
                                </span>
                            @endif
                        </td>

                        <!-- Jam Absen -->
                        <td class="py-2.5 px-3.5 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="font-bold text-slate-800 text-xs">
                                    {{ \Carbon\Carbon::parse($log->jam_absen)->format('d M Y') }}
                                </span>
                                <span class="inline-flex items-center gap-1 font-mono text-[10.5px] font-bold text-emerald-700 mt-0.5">
                                    <i class="ti ti-clock text-[10px]"></i>
                                    {{ \Carbon\Carbon::parse($log->jam_absen)->format('H:i:s') }}
                                </span>
                            </div>
                        </td>

                        <!-- Scan Type -->
                        <td class="py-2.5 px-3.5 text-center">
                            @php
                                $status_scan = (string) $log->status_scan;
                                $label = 'Scan ' . $status_scan;
                                $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                
                                if($status_scan === '0') {
                                    $label = 'Masuk (0)';
                                    $badgeClass = 'bg-sky-50 text-sky-700 border-sky-200';
                                } elseif($status_scan === '1') {
                                    $label = 'Pulang (1)';
                                    $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                } elseif($status_scan === '2') {
                                    $label = 'Istirahat Out (2)';
                                    $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                } elseif($status_scan === '3') {
                                    $label = 'Istirahat In (3)';
                                    $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                } elseif($status_scan === '4') {
                                    $label = 'Lembur In (4)';
                                    $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                } elseif($status_scan === '5') {
                                    $label = 'Lembur Out (5)';
                                    $badgeClass = 'bg-purple-50 text-purple-700 border-purple-200';
                                }
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold border {{ $badgeClass }}">
                                {{ $label }}
                            </span>
                        </td>

                        <!-- Mesin -->
                        <td class="py-2.5 px-4">
                            <div class="flex items-center gap-1.5 text-slate-700 font-semibold">
                                <i class="ti ti-fingerprint text-slate-400 text-sm"></i>
                                <span>{{ $log->nama_mesin ?? 'ID: ' . $log->id_mesin }}</span>
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="py-2.5 px-3.5 text-center">
                            @if($log->status == 1)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="ti ti-check text-[10px]"></i> Success
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <i class="ti ti-x text-[10px]"></i> Failed
                                </span>
                            @endif
                        </td>

                        <!-- Keterangan -->
                        <td class="py-2.5 px-4">
                            <span class="text-[11px] text-slate-500 font-normal">
                                {{ $log->keterangan ?: '-' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-10 text-center text-slate-400">
                            <i class="ti ti-device-desktop-search text-3xl mb-2 block text-slate-300"></i>
                            <span>Tidak ada data log mesin presensi yang ditemukan.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($logs->hasPages())
            <div class="px-5 py-3.5 bg-white border-t border-slate-100">
                {{ $logs->links('v2.components.pagination') }}
            </div>
        @endif
    </div>

</div>

@endsection

@push('myscript')
<script>
    $(function() {
        if (typeof flatpickr !== 'undefined') {
            $('.flatpickr-date').flatpickr({
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd M Y',
                allowInput: false
            });
        }
    });
</script>
@endpush
