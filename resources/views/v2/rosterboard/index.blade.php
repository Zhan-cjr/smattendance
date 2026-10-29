@extends('v2.layouts.app')

@section('titlepage', 'Roster Board')
@section('page_title', 'Roster Board (Live Schedule Matrix)')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';

    $totalKaryawan = count($matrix);
    $totalShiftAktif = count($allJamKerja);
    $totalUnderstaffed = count($understaffedAlerts ?? []);
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTIONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-layout-grid text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Roster Board (Live Schedule Matrix)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Matriks visual jadwal kerja aktif seluruh karyawan real-time. Klik sel shift untuk ubah instan atau gunakan mode swap shift antar karyawan.
            </p>
        </div>

        <!-- Right Side: Breadcrumbs -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-calendar-time text-sm"></i>
                    <span>Jadwal</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Roster Board</span>
            </nav>
        </div>
    </div>

    <!-- ================= 2. STATUS METRICS ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-layout-grid text-[200px]"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            <!-- Segment 1: Total Karyawan Ditampilkan -->
            <div class="flex-1 min-w-[140px] sm:min-w-[160px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Karyawan Aktif
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/30">
                            Matriks
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalKaryawan) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-users text-xs opacity-80"></i>
                        <span>Personel Terdaftar</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/20 px-2 py-0.5 rounded-md">
                        Terjadwal
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Pilihan Shift Tersedia -->
            <div class="flex-1 min-w-[140px] sm:min-w-[160px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Master Shift
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-400/30 text-emerald-100 border border-emerald-300/40">
                            Jam Kerja
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalShiftAktif) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-clock-check text-xs opacity-80"></i>
                        <span>Shift Terdaftar</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/20 px-2 py-0.5 rounded-md">
                        Aktif
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Peringatan Staf / Understaffed Alert -->
            <div class="flex-1 min-w-[140px] sm:min-w-[160px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-rose-100 truncate">
                            Status Kuota Personel
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $totalUnderstaffed > 0 ? 'bg-rose-400/30 text-rose-100 border border-rose-300/40' : 'bg-emerald-400/30 text-emerald-100 border border-emerald-300/40' }}">
                            {{ $totalUnderstaffed > 0 ? 'Kurang Staf' : 'Aman' }}
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black {{ $totalUnderstaffed > 0 ? 'text-rose-100' : 'text-white' }} tracking-tight mt-1.5">
                        {{ number_format($totalUnderstaffed) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti {{ $totalUnderstaffed > 0 ? 'ti-alert-triangle' : 'ti-shield-check' }} text-xs opacity-80"></i>
                        <span>{{ $totalUnderstaffed > 0 ? 'Shift Butuh Personel' : 'Semua Kuota Terpenuhi' }}</span>
                    </span>
                    <span class="font-black text-white text-[10px] {{ $totalUnderstaffed > 0 ? 'bg-rose-500/30 text-rose-100' : 'bg-white/20' }} px-2 py-0.5 rounded-md">
                        Monitoring
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. FILTER TOOLBAR (KONSISTEN V2) ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('rosterboard.index') }}" method="GET" class="w-full space-y-2.5 sm:space-y-3">
            
            <!-- BARIS 1: BULAN & TAHUN -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3 w-full">
                <!-- Bulan -->
                <div class="relative w-full">
                    <select name="bulan" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-semibold shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Tahun -->
                <div class="relative w-full">
                    <select name="tahun" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-semibold shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                        @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- BARIS 2: SEARCH NAMA/NIK, CABANG, DEPARTEMEN & TOMBOL AKSI -->
            <div class="flex flex-col lg:flex-row items-center gap-2.5 sm:gap-3 w-full">
                
                <!-- Search Input Nama / NIK -->
                <div class="flex-1 min-w-0 w-full relative">
                    <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari Nama Karyawan / NIK..." 
                           class="w-full pl-11 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <!-- Cabang Filter -->
                <div class="w-full sm:w-auto lg:w-44 shrink-0">
                    <select name="kode_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ $kode_cabang == $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Departemen Filter -->
                <div class="w-full sm:w-auto lg:w-44 shrink-0">
                    <select name="kode_dept" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Dept</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ $kode_dept == $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons: Cari & Reset -->
                <div class="flex items-center gap-1.5 shrink-0 w-full lg:w-auto">
                    <button type="submit" 
                            class="w-full lg:w-auto px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-search text-sm"></i>
                        <span>Cari</span>
                    </button>
                    @if(request()->hasAny(['search', 'kode_cabang', 'kode_dept', 'bulan', 'tahun']))
                        <a href="{{ route('rosterboard.index') }}" 
                           class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1 active:scale-95"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <!-- Peringatan Kekurangan Staf Jika Ada -->
        @if (!empty($understaffedAlerts))
            <div class="mt-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2.5">
                <i class="ti ti-alert-triangle text-base text-rose-600 shrink-0 mt-0.5"></i>
                <div class="flex-1 leading-relaxed">
                    <span class="font-bold text-rose-900">Peringatan Kuota Minimum Shift:</span>
                    <span>Terdeteksi {{ count($understaffedAlerts) }} jadwal shift di bawah kuota minimum.</span>
                    <div class="flex items-center gap-1.5 flex-wrap mt-1.5">
                        @foreach (array_slice($understaffedAlerts, 0, 4) as $alert)
                            <span class="px-2 py-0.5 rounded-md bg-white border border-rose-200 font-bold text-[10.5px] text-rose-700 shadow-2xs">
                                Tgl {{ $alert['hari'] }} {{ $alert['shift'] }}: {{ $alert['actual'] }}/{{ $alert['required'] }} org
                            </span>
                        @endforeach
                        @if(count($understaffedAlerts) > 4)
                            <span class="text-[10px] text-rose-500 font-medium">dan {{ count($understaffedAlerts) - 4 }} lainnya...</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- ================= 4. PALETTE SHIFT & QUICK TOOLS ================= -->
    <div class="bg-white rounded-2xl p-3 sm:p-3.5 border border-slate-200/90 shadow-2xs flex items-center justify-between flex-wrap gap-3">
        <!-- Palette Tools -->
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-xs font-bold text-slate-700 mr-1 flex items-center gap-1">
                <i class="ti ti-palette text-emerald-600 text-sm"></i>
                <span>Palette Shift:</span>
            </span>
            
            <!-- Tombol Libur (OFF) -->
            <button type="button" 
                    class="roster-palette-btn active px-2.5 py-1.5 rounded-xl border text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer"
                    data-code="" 
                    data-name="OFF" 
                    data-color="#64748b"
                    style="border-color: #cbd5e1; background-color: #f1f5f9; color: #475569;">
                <span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span>
                <span>Libur (OFF)</span>
            </button>

            <!-- Shift List -->
            @foreach ($allJamKerja as $jk)
                @php
                    $jkColor = !empty($jk->color) ? $jk->color : '#059669';
                @endphp
                <button type="button" 
                        class="roster-palette-btn px-2.5 py-1.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer"
                        data-code="{{ $jk->kode_jam_kerja }}" 
                        data-color="{{ $jkColor }}" 
                        data-name="{{ $jk->nama_jam_kerja }}">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $jkColor }};"></span>
                    <span class="text-slate-800">{{ $jk->nama_jam_kerja }}</span>
                    <span class="text-[10px] text-slate-400 font-mono font-medium">({{ $jk->jam_masuk }}-{{ $jk->jam_pulang }})</span>
                </button>
            @endforeach
        </div>

        <!-- Swap Shift Tool -->
        <div class="flex items-center gap-2">
            <button type="button" 
                    id="btnToggleSwapMode"
                    class="px-3.5 py-1.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer active:scale-95">
                <i class="ti ti-arrows-left-right text-sm text-emerald-600"></i>
                <span id="swapModeLabel">Mode Tukar Shift (Swap)</span>
            </button>
        </div>
    </div>

    <!-- ================= 5. ROSTER BOARD LIVE MATRIX TABLE ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-layout-grid"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Matriks Jadwal Kerja (Roster Board)</h3>
                </div>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                    {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                    {{ $totalKaryawan }} Karyawan
                </span>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-responsive w-full" style="max-height: 680px; overflow: auto;">
            <table class="w-full text-center align-middle border-collapse text-xs roster-table">
                <thead class="sticky top-0 z-20 shadow-xs select-none text-white font-bold uppercase tracking-wider text-[11px]"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-b border-black/15 text-white/95">
                        <!-- Sticky Employee Column -->
                        <th class="sticky left-0 z-30 px-3.5 py-3 text-left font-bold min-w-[220px] border-r border-black/15 shadow-md text-white"
                            style="background-color: {{ $themeColor1 }};">
                            Karyawan
                        </th>

                        <!-- Day Columns (1..DaysInMonth) -->
                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dateObj = \Carbon\Carbon::create($tahun, $bulan, $d);
                                $isSunday = $dateObj->isSunday();
                                $dayName = $dateObj->translatedFormat('D');
                            @endphp
                            <th class="p-1 min-w-[44px] border-r border-black/10 {{ $isSunday ? 'bg-rose-900/60 text-rose-100 font-black' : 'text-white/90' }}">
                                <div class="text-[9.5px] opacity-85 uppercase leading-none font-medium">{{ $dayName }}</div>
                                <div class="text-xs font-black mt-0.5">{{ $d }}</div>
                            </th>
                        @endfor

                        <!-- Summary Column -->
                        <th class="px-3 py-3 text-white font-bold min-w-[65px] border-l border-black/15"
                            style="background-color: {{ $themeColor1 }};">
                            Total
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 font-sans bg-white">
                    @forelse ($matrix as $nik => $row)
                        @php
                            $karyawan = $row['karyawan'];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition group">
                            
                            <!-- Sticky Karyawan Column (with Photo) -->
                            <td class="sticky left-0 bg-white z-10 px-3.5 py-2.5 text-left border-r border-slate-200/90 shadow-xs group-hover:bg-slate-50/90 transition">
                                <div class="flex items-center gap-2.5">
                                    <!-- Foto Avatar -->
                                    <div class="relative shrink-0">
                                        @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                                            <img src="{{ getfotoKaryawan($karyawan->foto) }}" alt="{{ $karyawan->nama_karyawan }}" class="w-8 h-9 rounded-lg object-cover border border-slate-200 shadow-2xs">
                                        @else
                                            <div class="w-8 h-9 rounded-lg text-white font-black text-xs flex items-center justify-center shadow-2xs"
                                                 style="background-color: {{ $themeColor1 }};">
                                                {{ strtoupper(substr($karyawan->nama_karyawan ?? $nik, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Nama & NIK / Dept / Cabang -->
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-900 truncate max-w-[150px] text-xs leading-tight" title="{{ $karyawan->nama_karyawan ?? $nik }}">
                                            {{ $karyawan->nama_karyawan ?? $nik }}
                                        </div>
                                        <div class="text-[10.5px] text-slate-500 font-mono truncate max-w-[150px] mt-0.5">
                                            {{ $karyawan->nik_show ?? $nik }} <span class="text-slate-300">•</span> {{ $karyawan->departemen->nama_dept ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>                            <!-- Day Cells (1..N) -->
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $cell = $row['schedule'][$d] ?? null;
                                    $targetDateStr = \Carbon\Carbon::create($tahun, $bulan, $d)->format('Y-m-d');
                                    $dateObj = \Carbon\Carbon::create($tahun, $bulan, $d);
                                    $isSunday = $dateObj->isSunday();
                                    $jkCode = $cell ? $cell['kode_jam_kerja'] : null;
                                    $cellColor = $cell && !empty($cell['color']) ? $cell['color'] : '#059669';
                                    $isOff = empty($jkCode);
                                @endphp
                                <td class="p-0 border-r border-b border-black/10 roster-cell relative" 
                                    data-nik="{{ $nik }}" 
                                    data-karyawan-name="{{ $karyawan->nama_karyawan }}"
                                    data-tanggal="{{ $targetDateStr }}"
                                    data-current-jk="{{ $jkCode ?? '' }}"
                                    data-color="{{ $isOff ? '#f8fafc' : $cellColor }}"
                                    style="cursor: pointer; background-color: {{ $isOff ? ($isSunday ? '#fff1f2' : '#f8fafc') : $cellColor }}; height: 38px;"
                                    title="{{ $karyawan->nama_karyawan }} (Tgl {{ $d }}): {{ $cell['nama_jam_kerja'] }}">
                                    
                                    <div class="w-full h-full min-h-[38px] flex items-center justify-center px-1 text-center select-none font-black text-[10.5px] tracking-tight transition duration-150 {{ $isOff ? ($isSunday ? 'text-rose-400 font-bold' : 'text-slate-400 font-bold') : 'text-white' }}">
                                        {{ $cell['nama_jam_kerja'] }}
                                    </div>
                                </td>
                            @endfor

                            <!-- Summary / Total Hari Kerja -->
                            <td class="px-2.5 py-2 font-mono font-bold text-slate-800 text-center border-l border-slate-200/90 bg-slate-50/50">
                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[11px] font-black">
                                    {{ $row['stats']['total_kerja'] }}H
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 2 }}" class="p-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-2">
                                    <i class="ti ti-calendar-off"></i>
                                </div>
                                <span class="font-bold text-slate-600 block text-xs">Tidak Ada Data Karyawan</span>
                                <span class="text-[11px]">Tidak ada data karyawan yang cocok dengan kriteria filter pencarian.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
.roster-cell:hover {
    filter: brightness(0.92);
    outline: 2px solid #10b981;
    outline-offset: -2px;
}
.swap-selected {
    outline: 3px solid #f59e0b !important;
    outline-offset: -3px;
    animation: rosterPulse 1s infinite;
}
@keyframes rosterPulse {
    0% { transform: scale(0.97); }
    50% { transform: scale(1.02); }
    100% { transform: scale(0.97); }
}
</style>

@endsection

@push('myscript')
<script>
$(document).ready(function() {
    let selectedPaletteCode = '';
    let selectedPaletteName = 'OFF';
    let selectedPaletteColor = '#64748b';

    let isSwapMode = false;
    let firstSwapSelection = null; // {nik, tanggal, name, el}

    $('.roster-palette-btn').click(function() {
        $('.roster-palette-btn').removeClass('active ring-2 ring-emerald-500 bg-emerald-50');
        $(this).addClass('active ring-2 ring-emerald-500 bg-emerald-50');

        selectedPaletteCode = $(this).data('code') || '';
        selectedPaletteName = $(this).data('name') || 'OFF';
        selectedPaletteColor = $(this).data('color') || '#64748b';

        if (isSwapMode) {
            toggleSwapMode();
        }
    });

    $('#btnToggleSwapMode').click(function() {
        toggleSwapMode();
    });

    function toggleSwapMode() {
        isSwapMode = !isSwapMode;
        if (isSwapMode) {
            $('#btnToggleSwapMode').removeClass('border-slate-300 text-slate-700').addClass('bg-amber-500 border-amber-600 text-white');
            $('#swapModeLabel').text('Mode Swap Aktif (Pilih 2 Sel)');
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Mode Tukar Shift Aktif: Klik sel Karyawan A lalu klik sel Karyawan B untuk menukar jadwal.',
                showConfirmButton: false,
                timer: 3500
            });
        } else {
            $('#btnToggleSwapMode').removeClass('bg-amber-500 border-amber-600 text-white').addClass('border-slate-300 text-slate-700');
            $('#swapModeLabel').text('Mode Tukar Shift (Swap)');
            if (firstSwapSelection) {
                firstSwapSelection.el.removeClass('swap-selected');
                firstSwapSelection = null;
            }
        }
    }

    // Handle Cell Click
    $('.roster-cell').click(function() {
        let cell = $(this);
        let nik = cell.data('nik');
        let tanggal = cell.data('tanggal');
        let name = cell.data('karyawan-name');
        let currentJk = cell.attr('data-current-jk') || '';
        let currentJkText = cell.text().trim() || 'OFF';

        if (isSwapMode) {
            // Logika Swap Shift
            if (!firstSwapSelection) {
                firstSwapSelection = { nik: nik, tanggal: tanggal, name: name, jk: currentJk, jkText: currentJkText, el: cell };
                cell.addClass('swap-selected');
            } else {
                let second = { nik: nik, tanggal: tanggal, name: name, jk: currentJk, jkText: currentJkText, el: cell };
                
                // Mencegah klik di sel yang sama persis
                if (firstSwapSelection.nik === second.nik && firstSwapSelection.tanggal === second.tanggal) {
                    firstSwapSelection.el.removeClass('swap-selected');
                    firstSwapSelection = null;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Silakan pilih sel karyawan atau tanggal yang berbeda untuk ditukar.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }

                cell.addClass('swap-selected');

                Swal.fire({
                    title: 'Konfirmasi Tukar Shift',
                    html: `
                        <div class="text-xs text-left p-3 rounded-xl bg-slate-50 border border-slate-200 mt-2 space-y-2">
                            <div class="flex items-center justify-between pb-1.5 border-b border-slate-200">
                                <div>
                                    <span class="font-bold text-slate-800">${firstSwapSelection.name}</span>
                                    <div class="text-[11px] text-slate-500">${firstSwapSelection.tanggal}</div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[11px] font-black bg-slate-200 text-slate-700">${firstSwapSelection.jkText}</span>
                            </div>
                            <div class="text-center text-emerald-600 font-bold text-sm">
                                <i class="ti ti-arrows-down-up"></i> Tukar Dengan
                            </div>
                            <div class="flex items-center justify-between pt-1.5 border-t border-slate-200">
                                <div>
                                    <span class="font-bold text-slate-800">${second.name}</span>
                                    <div class="text-[11px] text-slate-500">${second.tanggal}</div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[11px] font-black bg-slate-200 text-slate-700">${second.jkText}</span>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Tukar Sekarang',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl'
                    }
                }).then((res) => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url: "{{ route('rosterboard.swapShift') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                nik_1: firstSwapSelection.nik,
                                tanggal_1: firstSwapSelection.tanggal,
                                kode_jk_1: firstSwapSelection.jk,
                                nik_2: second.nik,
                                tanggal_2: second.tanggal,
                                kode_jk_2: second.jk
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.error : 'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                    firstSwapSelection.el.removeClass('swap-selected');
                    second.el.removeClass('swap-selected');
                    firstSwapSelection = null;
                    toggleSwapMode();
                });
            }
        } else {
            // Logika Single Update Shift
            let isOff = (selectedPaletteCode === '');
            let cellBg = isOff ? '#f8fafc' : selectedPaletteColor;
            let textClass = isOff ? 'text-slate-400 font-bold' : 'text-white font-black';

            let newHtml = `<div class="w-full h-full min-h-[38px] flex items-center justify-center px-1 text-center select-none font-black text-[10.5px] tracking-tight transition duration-150 ${textClass}">${selectedPaletteName}</div>`;
            
            cell.css('background-color', cellBg);
            cell.html(newHtml);
            cell.attr('data-current-jk', selectedPaletteCode);
            cell.attr('data-color', cellBg);

            $.ajax({
                url: "{{ route('rosterboard.updateShift') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    nik: nik,
                    tanggal: tanggal,
                    kode_jam_kerja: selectedPaletteCode
                },
                success: function(res) {
                    // Success real-time
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Update Shift',
                        text: xhr.responseJSON ? xhr.responseJSON.error : 'Terjadi kesalahan sistem',
                        customClass: {
                            popup: 'rounded-2xl shadow-xl'
                        }
                    });
                }
            });
        }
    });
});
</script>
@endpush
