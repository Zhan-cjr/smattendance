@extends('v2.layouts.app')

@section('titlepage', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

@php
    $authUser = auth()->user();
    $fullName = $authUser->name ?? 'Pengguna';
    $userName = explode(' ', $fullName)[0];
    $userEmail = $authUser->email ?? '-';
    $userRole = $authUser->getRoleNames()->first() ?? 'Super Admin';
    $userPhoto = null;
    $userDept = 'Semua Departemen';
    $userCabang = $general_setting->nama_perusahaan ?? 'Kantor Pusat';
    $userPhone = $general_setting->telepon ?? '-';
    $userJoinDate = $authUser->created_at ? $authUser->created_at->translatedFormat('d M Y') : '-';

    $userKaryawan = \App\Models\Userkaryawan::where('id_user', $authUser->id)->first();
    if ($userKaryawan) {
        $karyawanProfile = \App\Models\Karyawan::where('karyawan.nik', $userKaryawan->nik)
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select('karyawan.*', 'departemen.nama_dept', 'cabang.nama_cabang')
            ->first();

        if ($karyawanProfile) {
            $fullName = $karyawanProfile->nama_karyawan ?? $fullName;
            $userName = explode(' ', $fullName)[0];
            if ($karyawanProfile->nama_dept) {
                $userDept = $karyawanProfile->nama_dept;
            }
            if ($karyawanProfile->nama_cabang) {
                $userCabang = $karyawanProfile->nama_cabang;
            }
            if ($karyawanProfile->no_hp) {
                $userPhone = $karyawanProfile->no_hp;
            }
            if ($karyawanProfile->tgl_masuk) {
                $userJoinDate = \Carbon\Carbon::parse($karyawanProfile->tgl_masuk)->translatedFormat('d M Y');
            }
            if ($karyawanProfile->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('/karyawan/' . $karyawanProfile->foto)) {
                $userPhoto = getfotoKaryawan($karyawanProfile->foto);
            }
        }
    }

    // Hitung Cabang & Departemen yang dapat diakses oleh user
    if ($authUser->isSuperAdmin()) {
        $accessibleCabangText = 'Semua Cabang';
        $accessibleDeptText = 'Semua Departemen';
    } else {
        $cabangs = $authUser->getCabang();
        $deptList = $authUser->getDepartemen();

        $accessibleCabangText = $cabangs->isNotEmpty() ? $cabangs->pluck('nama_cabang')->implode(', ') : '-';
        $accessibleDeptText = $deptList->isNotEmpty() ? $deptList->pluck('nama_dept')->implode(', ') : '-';
    }

    $totalKaryawan = $status_karyawan->jml_aktif ?? 0;
    $totalHadir = $rekappresensi->hadir ?? 0;
    $totalIzin = $rekappresensi->izin ?? 0;
    $totalSakit = $rekappresensi->sakit ?? 0;
    $totalCuti = $rekappresensi->cuti ?? 0;

    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';

    // Statistik Pendidikan Karyawan (Sesuai Hak Akses Cabang & Dept)
    $queryPendidikan = \App\Models\Karyawan::query()
        ->select('pendidikan_terakhir', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
        ->where('status_aktif_karyawan', 1);

    if (!$authUser->isSuperAdmin()) {
        $userCabangCodes = $authUser->getCabangCodes();
        $userDeptCodes = $authUser->getDepartemenCodes();
        if (!empty($userCabangCodes)) {
            $queryPendidikan->whereIn('karyawan.kode_cabang', $userCabangCodes);
        }
        if (!empty($userDeptCodes)) {
            $queryPendidikan->whereIn('karyawan.kode_dept', $userDeptCodes);
        }
    }
    if (request('kode_cabang')) {
        $queryPendidikan->where('karyawan.kode_cabang', request('kode_cabang'));
    }
    if (request('kode_dept')) {
        $queryPendidikan->where('karyawan.kode_dept', request('kode_dept'));
    }

    $rawPendidikan = $queryPendidikan->groupBy('pendidikan_terakhir')->pluck('total', 'pendidikan_terakhir')->toArray();
    $pendidikanLevels = [
        'SD' => ['label' => 'SD', 'color' => '#ef4444'],
        'SMP' => ['label' => 'SMP', 'color' => '#f97316'],
        'SMA' => ['label' => 'SMA', 'color' => '#f59e0b'],
        'SMK' => ['label' => 'SMK', 'color' => '#eab308'],
        'D1' => ['label' => 'D1', 'color' => '#84cc16'],
        'D2' => ['label' => 'D2', 'color' => '#22c55e'],
        'D3' => ['label' => 'D3', 'color' => '#10b981'],
        'D4' => ['label' => 'D4', 'color' => '#06b6d4'],
        'S1' => ['label' => 'S1', 'color' => '#3b82f6'],
        'S2' => ['label' => 'S2', 'color' => '#6366f1'],
        'S3' => ['label' => 'S3', 'color' => '#8b5cf6'],
    ];

    $filteredPendidikan = [];
    $totalKaryawanPendidikan = 0;
    foreach ($pendidikanLevels as $code => $info) {
        $count = (int)($rawPendidikan[$code] ?? 0);
        if ($count > 0) {
            $filteredPendidikan[$code] = [
                'label' => $info['label'],
                'count' => $count,
                'color' => $info['color']
            ];
            $totalKaryawanPendidikan += $count;
        }
    }
    // Fallback jika belum ada data pendidikan terisi
    if (empty($filteredPendidikan)) {
        $filteredPendidikan = [
            'SMA/SMK' => ['label' => 'SMA/SMK', 'count' => 0, 'color' => '#f59e0b'],
            'D3' => ['label' => 'D3', 'count' => 0, 'color' => '#10b981'],
            'S1' => ['label' => 'S1', 'count' => 0, 'color' => '#3b82f6'],
        ];
    }

    // Statistik Status Karyawan (Total, Aktif, Non-Aktif & Detail Status)
    $queryKaryawanStatus = \App\Models\Karyawan::query();
    if (!$authUser->isSuperAdmin()) {
        $userCabangCodes = $authUser->getCabangCodes();
        $userDeptCodes = $authUser->getDepartemenCodes();
        if (!empty($userCabangCodes)) {
            $queryKaryawanStatus->whereIn('karyawan.kode_cabang', $userCabangCodes);
        }
        if (!empty($userDeptCodes)) {
            $queryKaryawanStatus->whereIn('karyawan.kode_dept', $userDeptCodes);
        }
    }
    if (request('kode_cabang')) {
        $queryKaryawanStatus->where('karyawan.kode_cabang', request('kode_cabang'));
    }
    if (request('kode_dept')) {
        $queryKaryawanStatus->where('karyawan.kode_dept', request('kode_dept'));
    }

    $karyawanStatusCounts = (clone $queryKaryawanStatus)
        ->select(
            \Illuminate\Support\Facades\DB::raw('count(*) as total_semua'),
            \Illuminate\Support\Facades\DB::raw("SUM(IF(status_aktif_karyawan = '1', 1, 0)) as total_aktif"),
            \Illuminate\Support\Facades\DB::raw("SUM(IF(status_aktif_karyawan = '0' OR status_aktif_karyawan IS NULL, 1, 0)) as total_nonaktif")
        )->first();

    $statTotalSemua = $karyawanStatusCounts->total_semua ?? 0;
    $statTotalAktif = $karyawanStatusCounts->total_aktif ?? 0;
    $statTotalNonAktif = $karyawanStatusCounts->total_nonaktif ?? 0;
    $statAktifPercent = $statTotalSemua > 0 ? round(($statTotalAktif / $statTotalSemua) * 100) : 0;
    $statNonAktifPercent = $statTotalSemua > 0 ? round(($statTotalNonAktif / $statTotalSemua) * 100) : 0;

    // Statistik Status Ketenagakerjaan (Kontrak, Tetap, Probation, dll)
    $allStatusList = \Illuminate\Support\Facades\DB::table('status_karyawan')->get();
    $statusPalette = ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316'];
    $employmentStatusBreakdown = [];
    $totalStatusEmployees = 0;

    foreach ($allStatusList as $idx => $st) {
        $qSt = clone $queryKaryawanStatus;
        $countSt = $qSt->where('status_karyawan', $st->kode_status_karyawan)->count();
        $color = $statusPalette[$idx % count($statusPalette)];
        $employmentStatusBreakdown[] = [
            'kode' => $st->kode_status_karyawan,
            'nama' => $st->nama_status_karyawan,
            'count' => $countSt,
            'color' => $color,
        ];
        $totalStatusEmployees += $countSt;
    }

    // Statistik Performance 1 Bulan Terakhir (Per Tanggal: Tepat Waktu vs Terlambat)
    $bulanIniObj = \Carbon\Carbon::now(config('app.timezone'));
    $startDateMonth = $bulanIniObj->copy()->startOfMonth()->format('Y-m-d');
    $endDateMonth = $bulanIniObj->copy()->endOfMonth()->format('Y-m-d');
    $daysInMonth = $bulanIniObj->daysInMonth;

    $queryMonthlyAttendance = \App\Models\Presensi::query()
        ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
        ->leftJoin('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
        ->whereBetween('presensi.tanggal', [$startDateMonth, $endDateMonth])
        ->where('presensi.status', 'h');

    if (!$authUser->isSuperAdmin()) {
        $userCabangCodes = $authUser->getCabangCodes();
        $userDeptCodes = $authUser->getDepartemenCodes();
        if (!empty($userCabangCodes)) {
            $queryMonthlyAttendance->whereIn('karyawan.kode_cabang', $userCabangCodes);
        }
        if (!empty($userDeptCodes)) {
            $queryMonthlyAttendance->whereIn('karyawan.kode_dept', $userDeptCodes);
        }
    }
    if (request('kode_cabang')) {
        $queryMonthlyAttendance->where('karyawan.kode_cabang', request('kode_cabang'));
    }
    if (request('kode_dept')) {
        $queryMonthlyAttendance->where('karyawan.kode_dept', request('kode_dept'));
    }

    $rawMonthlyAttendance = $queryMonthlyAttendance->select(
        'presensi.tanggal',
        \Illuminate\Support\Facades\DB::raw("SUM(IF(presensi.jam_in IS NOT NULL AND (presensi.jam_in <= CONCAT(presensi.tanggal, ' ', IFNULL(presensi_jamkerja.jam_masuk, '08:00:00')) OR TIME(presensi.jam_in) <= IFNULL(presensi_jamkerja.jam_masuk, '08:00:00')), 1, 0)) as tepat_waktu"),
        \Illuminate\Support\Facades\DB::raw("SUM(IF(presensi.jam_in IS NOT NULL AND (presensi.jam_in > CONCAT(presensi.tanggal, ' ', IFNULL(presensi_jamkerja.jam_masuk, '08:00:00')) AND TIME(presensi.jam_in) > IFNULL(presensi_jamkerja.jam_masuk, '08:00:00')), 1, 0)) as terlambat"),
        \Illuminate\Support\Facades\DB::raw("COUNT(*) as total_hadir_harian")
    )->groupBy('presensi.tanggal')->get()->keyBy('tanggal');

    $perfChartLabels = [];
    $perfChartTepatWaktu = [];
    $perfChartTerlambat = [];
    $totalBulanTepatWaktu = 0;
    $totalBulanTerlambat = 0;

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dateKey = sprintf('%04d-%02d-%02d', $bulanIniObj->year, $bulanIniObj->month, $day);
        $perfChartLabels[] = sprintf('%02d', $day);
        
        $rec = $rawMonthlyAttendance->get($dateKey);
        $tw = $rec ? (int)$rec->tepat_waktu : 0;
        $lt = $rec ? (int)$rec->terlambat : 0;
        
        $perfChartTepatWaktu[] = $tw;
        $perfChartTerlambat[] = $lt;
        $totalBulanTepatWaktu += $tw;
        $totalBulanTerlambat += $lt;
    }

    $totalSemuaHadirBulan = $totalBulanTepatWaktu + $totalBulanTerlambat;
    $persenTepatWaktuBulan = $totalSemuaHadirBulan > 0 ? round(($totalBulanTepatWaktu / $totalSemuaHadirBulan) * 100) : 100;

    // Upcoming Holidays (Hari Libur & Cuti Bersama Mendatang)
    $todayStr = \Carbon\Carbon::now(config('app.timezone'))->format('Y-m-d');
    $queryHoliday = \App\Models\Harilibur::query()
        ->where('tanggal', '>=', $todayStr)
        ->orderBy('tanggal', 'asc');

    if (!$authUser->isSuperAdmin()) {
        $userCabangCodes = $authUser->getCabangCodes();
        if (!empty($userCabangCodes)) {
            $queryHoliday->where(function($q) use ($userCabangCodes) {
                $q->whereIn('kode_cabang', $userCabangCodes)
                  ->orWhereNull('kode_cabang')
                  ->orWhere('kode_cabang', '');
            });
        }
    }
    $upcomingHolidays = $queryHoliday->limit(4)->get();
    $nextHoliday = $upcomingHolidays->first();
    $daysToNextHoliday = $nextHoliday ? \Carbon\Carbon::parse($todayStr)->diffInDays(\Carbon\Carbon::parse($nextHoliday->tanggal), false) : null;
@endphp

<!-- Page Title & Top Breadcrumbs with Actions -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Dashboard</h1>
        <div class="flex items-center gap-1.5 text-xs text-slate-400 mt-0.5">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>/</span>
            <span class="text-slate-600 font-semibold">Dashboard</span>
        </div>
    </div>

    <!-- Top Action Buttons: Export & Date Picker -->
    <div class="flex items-center gap-2.5 self-start sm:self-auto">
        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200/90 rounded-lg text-xs font-semibold text-slate-700 shadow-2xs">
            <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-500"></i>
            <span>{{ \Carbon\Carbon::now(config('app.timezone'))->translatedFormat('d F Y') }}</span>
        </div>
    </div>
</div>

<!-- Leave Approval Info Banner (Blue Outline Alert) -->
<div class="mb-5 p-3 rounded-lg bg-sky-50/70 border border-sky-200/80 text-sky-800 flex items-center justify-between text-xs">
    <div class="flex items-center gap-2">
        <i data-lucide="info" class="w-4 h-4 text-sky-600"></i>
        <span>Sistem Presensi & Manajemen SDM <strong>{{ $general_setting->nama_perusahaan ?? 'Perusahaan' }}</strong> berjalan dengan baik.</span>
    </div>
    <button class="text-sky-500 hover:text-sky-800" onclick="this.parentElement.remove()">
        <i data-lucide="x" class="w-3.5 h-3.5"></i>
    </button>
</div>

<!-- ================= ROW 1: USER PROFILE CARD + LEAVE DETAILS DONUT + LEAVE SUMMARY TABLE ================= -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-5">
    
    <!-- 1. User Profile Card (4 cols) Full Theme Gradient & Subtle Minimalist Watermark -->
    <div class="lg:col-span-4 rounded-2xl p-5 shadow-lg flex flex-col justify-between relative overflow-hidden group transition-all duration-300 text-white select-none border border-white/10"
         style="background: linear-gradient(135deg, {{ $themeColor1 }} 0%, {{ $themeColor2 }} 100%);">
        
        <!-- Clean Minimalist Geometric Watermark (Right Bottom only) -->
        <svg class="absolute -right-8 -bottom-8 w-48 h-48 text-white/[0.06] pointer-events-none" viewBox="0 0 200 200" fill="none">
            <circle cx="160" cy="160" r="120" stroke="currentColor" stroke-width="1.5"/>
            <circle cx="160" cy="160" r="80" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4"/>
            <circle cx="160" cy="160" r="40" stroke="currentColor" stroke-width="1"/>
        </svg>

        <!-- Soft Ambient Corner Glow -->
        <div class="absolute -right-10 -top-10 w-36 h-36 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-start justify-between relative z-10">
            <div class="flex items-center gap-3.5 min-w-0">
                <!-- Avatar: Circular (rounded-full) with Ring & Active Indicator -->
                <div class="relative flex-shrink-0 w-12 h-12">
                    <div class="w-12 h-12 rounded-full p-0.5 bg-white/30 backdrop-blur-md shadow-md border border-white/40 flex items-center justify-center flex-shrink-0">
                        <div class="w-full h-full rounded-full overflow-hidden bg-white/10 flex items-center justify-center font-bold text-white text-sm aspect-square flex-shrink-0">
                            @if($userPhoto)
                                <img src="{{ $userPhoto }}" alt="{{ $fullName }}" class="w-full h-full object-cover rounded-full">
                            @else
                                <div class="w-full h-full rounded-full flex items-center justify-center font-bold text-base text-white bg-white/20 backdrop-blur-sm">
                                    {{ strtoupper(substr($fullName, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Small online status dot -->
                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-white ring-1 ring-black/10" title="Online / Aktif"></span>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5">
                        <h3 class="text-sm font-bold text-white truncate leading-tight tracking-tight drop-shadow-xs">{{ $fullName }}</h3>
                        <i data-lucide="badge-check" class="w-4 h-4 flex-shrink-0 text-white fill-white/20"></i>
                    </div>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-white/20 backdrop-blur-sm text-white border border-white/20 capitalize flex-shrink-0">
                            {{ $userRole }}
                        </span>
                        <span class="text-xs text-white/80 font-medium truncate">• {{ $userDept }}</span>
                    </div>
                </div>
            </div>

            <!-- Action dropdown -->
            <div class="relative" x-data="{ openCardMenu: false }">
                <button @click="openCardMenu = !openCardMenu" class="p-1.5 text-white/70 hover:text-white hover:bg-white/15 rounded-lg transition backdrop-blur-xs">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
                <div x-show="openCardMenu" 
                     @click.away="openCardMenu = false" 
                     x-cloak
                     class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-20 text-xs text-slate-800">
                    <a href="{{ route('profile.editprofile') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 transition">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Lihat Profil</span>
                    </a>
                    <a href="{{ route('generalsetting.index') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 transition">
                        <i data-lucide="settings" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Pengaturan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Data Info List (Clean Minimalist Original Style) -->
        <div class="mt-4 pt-3.5 border-t border-white/20 space-y-2 text-xs relative z-10">
            <div class="flex items-center justify-between">
                <span class="text-white/70">Phone Number</span>
                <span class="font-semibold text-white font-mono">{{ $userPhone }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-white/70">Email Address</span>
                <span class="font-semibold text-white truncate max-w-[190px]" title="{{ $userEmail }}">{{ $userEmail }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-white/70">Akses Cabang</span>
                <span class="font-semibold text-white truncate max-w-[190px]" title="{{ $accessibleCabangText }}">{{ $accessibleCabangText }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-white/70">Akses Departemen</span>
                <span class="font-semibold text-white truncate max-w-[190px]" title="{{ $accessibleDeptText }}">{{ $accessibleDeptText }}</span>
            </div>
        </div>

        <!-- Bottom Action Buttons: Edit Profile & General Setting -->
        <div class="mt-4 pt-3 border-t border-white/15 grid grid-cols-2 gap-2 relative z-10">
            <a href="{{ route('profile.editprofile') }}" 
               class="flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-lg bg-white/15 hover:bg-white/25 border border-white/20 text-white font-semibold text-xs backdrop-blur-xs transition shadow-2xs group/btn">
                <i data-lucide="user-pen" class="w-3.5 h-3.5 text-white/90 group-hover/btn:scale-110 transition-transform"></i>
                <span class="truncate">Edit Profile</span>
            </a>
            <a href="{{ route('generalsetting.index') }}" 
               class="flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-lg bg-white/15 hover:bg-white/25 border border-white/20 text-white font-semibold text-xs backdrop-blur-xs transition shadow-2xs group/btn">
                <i data-lucide="settings-2" class="w-3.5 h-3.5 text-white/90 group-hover/btn:rotate-45 transition-transform"></i>
                <span class="truncate">Pengaturan</span>
            </a>
        </div>
    </div>

    <!-- 2. Live Punch Attendance Box (4 cols - Real-time Animated Clock & Seconds Ring) -->
    <div class="lg:col-span-4 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between items-center text-center">
        <div class="w-full">
            <span class="text-xs font-medium text-slate-400 block tracking-wide">Attendance</span>
            <h4 class="text-base font-bold text-slate-800" id="liveFullDate">{{ date('h:i A, d M Y') }}</h4>
        </div>

        <!-- Realtime Animated SVG Seconds Circular Progress Ring -->
        <div class="my-3 relative flex items-center justify-center">
            <svg class="w-28 h-28 transform -rotate-90">
                <!-- Background track circle -->
                <circle cx="56" cy="56" r="46" stroke="#f1f5f9" stroke-width="6" fill="transparent" />
                <!-- Animated Seconds Progress Arc -->
                <circle id="secondsProgressRing" 
                        cx="56" cy="56" r="46" 
                        stroke="{{ $themeColor1 }}" 
                        stroke-width="6" 
                        stroke-linecap="round" 
                        stroke-dasharray="289.02" 
                        stroke-dashoffset="0" 
                        fill="transparent" 
                        style="transition: stroke-dashoffset 0.15s linear;" />
            </svg>

            <!-- Inner Display: Shows live seconds as requested -->
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-[9px] text-slate-400 uppercase font-semibold tracking-wider">DETIK</span>
                <span class="text-2xl font-black text-slate-800 font-mono tracking-tight leading-none my-0.5" id="smartSecondsOnly">00</span>
                <span class="text-[10px] font-bold text-slate-500 font-mono" id="smartClock">00:00 AM</span>
            </div>
        </div>

        <div class="w-full">
            <a href="{{ route('presensi.index') }}" 
               class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-white font-bold text-xs tracking-wide shadow-sm hover:opacity-95 active:scale-98 transition"
               style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }})">
                <i data-lucide="device-desktop" class="w-4 h-4"></i>
                <span>Monitoring Presensi</span>
            </a>
        </div>
    </div>

    <!-- 3. Status Karyawan Statistics Card (4 cols) -->
    <div class="lg:col-span-4 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between">
        <!-- Header -->
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white shadow-2xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }})">
                    <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 leading-tight">Status Karyawan</h3>
                    <span class="text-[10px] text-slate-400">Total & Keaktifan SDM</span>
                </div>
            </div>
            <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-lg">
                <i data-lucide="calendar" class="w-3 h-3 text-slate-400"></i>
                <span>{{ date('Y') }}</span>
            </div>
        </div>

        <!-- 3 Metric Boxes: Total Karyawan, Aktif, Non-Aktif -->
        <div class="grid grid-cols-3 gap-2.5 mb-3">
            <!-- Box 1: Total Karyawan -->
            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 hover:border-slate-200 transition">
                <div class="flex items-center gap-1 text-slate-500 mb-1">
                    <i data-lucide="users" class="w-3 h-3 text-slate-400"></i>
                    <span class="text-[10px] font-semibold">Total</span>
                </div>
                <div class="text-lg font-black text-slate-800 leading-tight">{{ $statTotalSemua }}</div>
                <span class="text-[10px] text-slate-400">Semua SDM</span>
            </div>

            <!-- Box 2: Aktif -->
            <div class="p-2.5 bg-emerald-50/60 rounded-xl border border-emerald-100 hover:border-emerald-200 transition">
                <div class="flex items-center gap-1 text-emerald-700 mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold">Aktif</span>
                </div>
                <div class="text-lg font-black text-emerald-600 leading-tight">{{ $statTotalAktif }}</div>
                <span class="text-[10px] text-emerald-600/80 font-medium">{{ $statAktifPercent }}% Aktif</span>
            </div>

            <!-- Box 3: Non-Aktif -->
            <div class="p-2.5 bg-slate-100/70 rounded-xl border border-slate-200/80 hover:border-slate-300 transition">
                <div class="flex items-center gap-1 text-slate-600 mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    <span class="text-[10px] font-bold">Non Aktif</span>
                </div>
                <div class="text-lg font-black text-slate-700 leading-tight">{{ $statTotalNonAktif }}</div>
                <span class="text-[10px] text-slate-500 font-medium">{{ $statNonAktifPercent }}% Keluar</span>
            </div>
        </div>

        <!-- Visual Progress Bar / Ratio & Sub-footer -->
        <div class="space-y-2 pt-2 border-t border-slate-100">
            <div class="flex items-center justify-between text-[11px]">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-600 font-medium">Rasio Keaktifan</span>
                </div>
                <span class="font-bold text-slate-800">{{ $statAktifPercent }}%</span>
            </div>
            <!-- Segmented Progress Bar -->
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden flex">
                <div class="bg-emerald-500 h-2 rounded-l-full transition-all duration-500" style="width: {{ $statAktifPercent }}%"></div>
                <div class="bg-slate-300 h-2 rounded-r-full transition-all duration-500" style="width: {{ $statNonAktifPercent }}%"></div>
            </div>
            <div class="flex items-center justify-between text-[10px] text-slate-400 pt-0.5">
                <span class="flex items-center gap-1">
                    <i data-lucide="shield-check" class="w-3 h-3 text-emerald-500"></i>
                    <span>Sesuai Hak Akses</span>
                </span>
                <span>{{ $statTotalAktif }} dari {{ $statTotalSemua }} Karyawan</span>
            </div>
        </div>
    </div>

</div>

<!-- ================= ROW 2: LEAVE DETAILS DONUT + 4 STAT CARDS & TIMELINE ================= -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-5">
    
    <!-- 1. Statistik Pendidikan Karyawan Donut Chart (4 cols) -->
    <div class="lg:col-span-4 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex flex-col h-full">
        <!-- Header -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-100/80">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white shadow-2xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }})">
                    <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 leading-tight">Pendidikan</h3>
                    <span class="text-[10px] text-slate-400">Distribusi Karyawan</span>
                </div>
            </div>
            <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-lg">
                <i data-lucide="users" class="w-3 h-3 text-slate-400"></i>
                <span>{{ $totalKaryawanPendidikan }} Total</span>
            </div>
        </div>

        <!-- Body: Legend & Donut Chart -->
        <div class="grid grid-cols-12 items-center gap-2 py-3 flex-1 min-h-0">
            <!-- Legend breakdown (Left 6 cols) -->
            <div class="col-span-6 space-y-0.5 text-[11px] max-h-[270px] overflow-y-auto pr-1 [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-slate-200 [&::-webkit-scrollbar-track]:bg-transparent">
                @forelse($filteredPendidikan as $item)
                    @php
                        $pct = $totalKaryawanPendidikan > 0 ? round(($item['count'] / $totalKaryawanPendidikan) * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-2 py-1 px-1 rounded-md hover:bg-slate-50 transition group">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 group-hover:scale-110 transition-transform" style="background-color: {{ $item['color'] }}"></span>
                        <span class="font-bold text-slate-700 min-w-[36px] group-hover:text-slate-900 transition-colors">{{ $item['label'] }}</span>
                        <div class="flex items-baseline gap-1 ml-auto sm:ml-0">
                            <span class="font-extrabold text-slate-800">{{ $item['count'] }}</span>
                            <span class="text-[10px] font-medium text-slate-400">({{ $pct }}%)</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-xs text-slate-400">
                        Belum ada data pendidikan
                    </div>
                @endforelse
            </div>

            <!-- Donut Canvas (Right 6 cols) -->
            <div class="col-span-6 relative flex items-center justify-center">
                <div class="w-36 h-36 sm:w-40 sm:h-40 relative flex items-center justify-center">
                    <canvas id="smartPendidikanChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider leading-none">TOTAL</span>
                        <span class="text-xl font-black text-slate-800 font-mono mt-1">{{ $totalKaryawanPendidikan }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 mt-auto">
            <span class="flex items-center gap-1">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
                <span>Filtered by Hak Akses</span>
            </span>
            <span class="text-[10px] font-semibold text-slate-400">{{ count($filteredPendidikan) }} Tingkat</span>
        </div>
    </div>

    <!-- 2. 4 Stat Cards + Productivity Bar (8 cols) -->
    <div class="lg:col-span-8 flex flex-col justify-between gap-4">
        
        <!-- 4 Real Kehadiran Statistics Cards Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            
            <!-- Card 1: Total Hadir -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs hover:shadow-md hover:border-emerald-300 transition-all duration-200 cursor-pointer group stat-card-clickable"
                 data-status="h">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live
                    </span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $totalHadir }}</span>
                    <span class="text-xs text-slate-400 font-medium">Karyawan</span>
                </div>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Total Hadir</p>
                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                    <span>Hari Ini</span>
                    <span class="text-emerald-600 font-bold group-hover:underline flex items-center gap-0.5">
                        Lihat <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </div>

            <!-- Card 2: Izin Resmi -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs hover:shadow-md hover:border-sky-300 transition-all duration-200 cursor-pointer group stat-card-clickable"
                 data-status="i">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[10px] font-bold text-sky-600 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-100">
                        Izin
                    </span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $totalIzin }}</span>
                    <span class="text-xs text-slate-400 font-medium">Karyawan</span>
                </div>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Izin Resmi</p>
                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                    <span>Terverifikasi</span>
                    <span class="text-sky-600 font-bold group-hover:underline flex items-center gap-0.5">
                        Lihat <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </div>

            <!-- Card 3: Sakit -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs hover:shadow-md hover:border-amber-300 transition-all duration-200 cursor-pointer group stat-card-clickable"
                 data-status="s">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">
                        Sakit
                    </span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $totalSakit }}</span>
                    <span class="text-xs text-slate-400 font-medium">Karyawan</span>
                </div>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Sedang Sakit</p>
                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                    <span>Surat Dokter</span>
                    <span class="text-amber-600 font-bold group-hover:underline flex items-center gap-0.5">
                        Lihat <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </div>

            <!-- Card 4: Cuti -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs hover:shadow-md hover:border-indigo-300 transition-all duration-200 cursor-pointer group stat-card-clickable"
                 data-status="c">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="briefcase" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">
                        Cuti
                    </span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $totalCuti }}</span>
                    <span class="text-xs text-slate-400 font-medium">Karyawan</span>
                </div>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Sedang Cuti</p>
                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                    <span>Terjadwal</span>
                    <span class="text-indigo-600 font-bold group-hover:underline flex items-center gap-0.5">
                        Lihat <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </div>

        </div>

        <!-- Status Ketenagakerjaan (Kontrak, Tetap, Probation, dll) Visual Bar & Metrics -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between mb-3.5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white shadow-2xs"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }})">
                        <i data-lucide="file-badge" class="w-3.5 h-3.5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 leading-tight">Status Ketenagakerjaan</h3>
                        <span class="text-[10px] text-slate-400">Distribusi Status Kontrak & Tetap</span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-lg">
                    <i data-lucide="users" class="w-3 h-3 text-slate-400"></i>
                    <span>{{ $totalStatusEmployees }} Total Karyawan</span>
                </div>
            </div>

            <!-- Dynamic Metrics Grid for Each Status (Kontrak, Tetap, dll) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-{{ min(max(count($employmentStatusBreakdown), 2), 4) }} gap-3 mb-3.5">
                @forelse($employmentStatusBreakdown as $st)
                    @php
                        $pct = $totalStatusEmployees > 0 ? round(($st['count'] / $totalStatusEmployees) * 100) : 0;
                    @endphp
                    <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 hover:border-slate-200 transition">
                        <span class="text-slate-500 text-[11px] font-medium block flex items-center gap-1.5 truncate mb-1">
                            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $st['color'] }}"></span>
                            <span class="truncate">{{ $st['nama'] }}</span>
                        </span>
                        <div class="flex items-baseline gap-1.5">
                            <strong class="text-slate-800 text-base font-black">{{ $st['count'] }}</strong>
                            <span class="text-[10px] font-semibold text-slate-400">({{ $pct }}%)</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-2 text-xs text-slate-400">
                        Belum ada status karyawan
                    </div>
                @endforelse
            </div>

            <!-- Segmented Color Timeline / Distribution Bar -->
            <div class="h-3 w-full bg-slate-100 rounded-full flex overflow-hidden gap-1 p-0.5">
                @foreach($employmentStatusBreakdown as $st)
                    @php
                        $widthPct = $totalStatusEmployees > 0 ? max(round(($st['count'] / $totalStatusEmployees) * 100), ($st['count'] > 0 ? 3 : 0)) : 0;
                    @endphp
                    @if($st['count'] > 0)
                        <div class="rounded-full h-full transition-all duration-500" 
                             style="width: {{ $widthPct }}%; background-color: {{ $st['color'] }};"
                             title="{{ $st['nama'] }}: {{ $st['count'] }} Karyawan"></div>
                    @endif
                @endforeach
            </div>

            <!-- Legend breakdown below the bar -->
            <div class="flex flex-wrap items-center justify-between gap-2 text-[10px] text-slate-400 mt-2.5 pt-2 border-t border-slate-100">
                <div class="flex flex-wrap items-center gap-3">
                    @foreach($employmentStatusBreakdown as $st)
                        <span class="flex items-center gap-1 text-slate-600 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $st['color'] }}"></span>
                            <span>{{ $st['nama'] }}: <strong>{{ $st['count'] }}</strong></span>
                        </span>
                    @endforeach
                </div>
                <span class="flex items-center gap-1 text-emerald-600 font-medium">
                    <i data-lucide="shield-check" class="w-3 h-3 text-emerald-500"></i>
                    <span>Sesuai Hak Akses</span>
                </span>
            </div>
        </div>

    </div>

</div>

<!-- ================= ROW 3: PERFORMANCE CHART + SKILLS PROGRESS + TEAM BIRTHDAY & HOLIDAYS ================= -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-5">
    
    <!-- 1. Performance Monthly Attendance Chart (5 cols - Tepat Waktu vs Terlambat) -->
    <div class="lg:col-span-5 bg-white rounded-xl p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white shadow-2xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }})">
                    <i data-lucide="bar-chart-3" class="w-3.5 h-3.5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 leading-tight">Performa Kehadiran</h3>
                    <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::now(config('app.timezone'))->translatedFormat('F Y') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-lg">
                <i data-lucide="calendar" class="w-3 h-3 text-slate-400"></i>
                <span>1 Bulan (Harian)</span>
            </div>
        </div>

        <div class="flex items-center justify-between my-2">
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-slate-900">{{ $persenTepatWaktuBulan }}%</span>
                <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tepat Waktu
                </span>
            </div>
            <!-- Legend Indicators -->
            <div class="flex items-center gap-3 text-[11px]">
                <span class="flex items-center gap-1 font-semibold text-emerald-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> {{ $totalBulanTepatWaktu }} Tepat
                </span>
                <span class="flex items-center gap-1 font-semibold text-rose-500">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> {{ $totalBulanTerlambat }} Terlambat
                </span>
            </div>
        </div>

        <div class="h-44 w-full relative">
            <canvas id="smartPerformanceChart"></canvas>
        </div>

        <div class="flex items-center justify-between text-[10px] text-slate-400 pt-2 mt-1 border-t border-slate-100">
            <span class="flex items-center gap-1">
                <i data-lucide="shield-check" class="w-3 h-3 text-emerald-500"></i>
                <span>Sesuai Hak Akses User</span>
            </span>
            <span>Grafik Performa Tanggal 01 - {{ $daysInMonth }}</span>
        </div>
    </div>

    <!-- 2. Karyawan Ulang Tahun Card (4 cols - Realtime Birthday List) -->
    <div class="lg:col-span-4 bg-white rounded-xl p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white bg-amber-500 shadow-2xs">
                        <i data-lucide="cake" class="w-3.5 h-3.5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 leading-tight">Ulang Tahun Hari Ini</h3>
                        <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::now(config('app.timezone'))->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-lg">
                    <i data-lucide="cake" class="w-3 h-3 text-amber-600"></i>
                    <span>{{ count($birthday ?? []) }} Karyawan</span>
                </span>
            </div>

            <!-- Birthday List Container -->
            <div class="space-y-2.5 max-h-[195px] overflow-y-auto pr-1">
                @forelse($birthday ?? [] as $b)
                    @php
                        $umur = \Carbon\Carbon::parse($b->tanggal_lahir)->age;
                    @endphp
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-amber-300 hover:bg-amber-50/20 transition-all duration-200 group">
                        <div class="flex items-center gap-3 truncate">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <div class="w-10 h-10 rounded-full overflow-hidden border border-slate-200 bg-amber-100 flex items-center justify-center font-bold text-amber-800 text-xs">
                                    @if (!empty($b->foto) && Storage::disk('public')->exists('/karyawan/' . $b->foto))
                                        <img src="{{ getfotoKaryawan($b->foto) }}" alt="{{ $b->nama_karyawan }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-amber-800 font-bold text-sm">{{ strtoupper(substr($b->nama_karyawan, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-xs border border-white">
                                    <i data-lucide="cake" class="w-2.5 h-2.5"></i>
                                </div>
                            </div>

                            <!-- Name & Department -->
                            <div class="truncate">
                                <h4 class="text-xs font-bold text-slate-800 truncate leading-tight group-hover:text-amber-700 transition-colors">{{ $b->nama_karyawan }}</h4>
                                <p class="text-[10px] text-slate-500 truncate mt-0.5 font-medium">
                                    {{ $b->nama_jabatan ?? '-' }} <span class="text-slate-300">•</span> {{ $b->nama_dept ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Age Badge & Birth Date -->
                        <div class="text-right flex-shrink-0 pl-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500 text-white">
                                {{ $umur }} Thn
                            </span>
                            <span class="block text-[9px] text-slate-400 mt-0.5 font-mono">{{ date('d M', strtotime($b->tanggal_lahir)) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <div class="w-10 h-10 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-1.5 border border-slate-200">
                            <i data-lucide="cake" class="w-4.5 h-4.5"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Tidak Ada yang Ulang Tahun Hari Ini</p>
                        <span class="text-[10px] text-slate-400">Tidak ada karyawan yang berulang tahun hari ini.</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Action Send WhatsApp Wishes Button -->
        <div class="pt-2 mt-3 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[10px] text-slate-400 flex items-center gap-1">
                <i data-lucide="shield-check" class="w-3 h-3 text-emerald-500"></i>
                <span>Sesuai Hak Akses</span>
            </span>
            @if(count($birthday ?? []) > 0)
                <button type="button" 
                        onclick="kirimUcapanSemuaV2()"
                        id="btnKirimUcapanV2"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-white font-semibold text-[11px] bg-emerald-600 hover:bg-emerald-700 transition">
                    <i data-lucide="send" class="w-3 h-3"></i>
                    <span id="btnTextV2">Kirim Ucapan WA</span>
                </button>
            @else
                <span class="text-[10px] text-slate-400 font-medium italic">Status Aktif</span>
            @endif
        </div>
    </div>

    <!-- 3. Upcoming Holidays & Calendar Widget (3 cols) -->
    <div class="lg:col-span-3 space-y-3.5 flex flex-col justify-between">
        
        <!-- Featured Next Holiday Card (Gradient Festive Box) -->
        <div class="rounded-2xl p-4 text-white shadow-xs relative overflow-hidden flex flex-col justify-between group transition-all duration-300 border border-white/10"
             style="background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);">
            
            <!-- Geometric curve watermark -->
            <svg class="absolute -right-6 -bottom-6 w-32 h-32 text-white/10 pointer-events-none" viewBox="0 0 200 200" fill="none">
                <circle cx="150" cy="150" r="100" stroke="currentColor" stroke-width="2"/>
                <circle cx="150" cy="150" r="60" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 4"/>
            </svg>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 backdrop-blur-xs text-white border border-white/20">
                        <i data-lucide="sparkles" class="w-3 h-3 text-amber-300"></i>
                        <span>Libur Terdekat</span>
                    </span>
                    @if($daysToNextHoliday !== null)
                        <span class="text-[10px] font-bold text-sky-100 bg-black/20 px-2 py-0.5 rounded-md">
                            {{ $daysToNextHoliday == 0 ? 'Hari Ini' : $daysToNextHoliday . ' Hari Lagi' }}
                        </span>
                    @endif
                </div>

                <h4 class="text-sm font-extrabold text-white leading-snug line-clamp-2 mt-1">
                    {{ $nextHoliday ? $nextHoliday->keterangan : 'Libur Nasional / Cuti Bersama' }}
                </h4>
                
                <div class="flex items-center gap-1.5 text-xs text-sky-100 font-medium mt-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-sky-200"></i>
                    <span>{{ $nextHoliday ? \Carbon\Carbon::parse($nextHoliday->tanggal)->translatedFormat('l, d F Y') : 'Menunggu jadwal libur' }}</span>
                </div>
            </div>

            <div class="mt-3 pt-2.5 border-t border-white/15 flex items-center justify-between">
                <span class="text-[10px] text-sky-100">Kalender Perusahaan</span>
                <a href="{{ route('harilibur.index') }}" 
                   class="inline-flex items-center gap-1 text-[11px] font-bold text-white bg-white/20 hover:bg-white/30 px-2.5 py-1 rounded-lg backdrop-blur-xs transition">
                    <span>Lihat Semua</span>
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
        </div>

        <!-- Upcoming Holidays Mini List / Policy Link -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex-1 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2.5">
                <div class="flex items-center gap-1.5">
                    <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-slate-500"></i>
                    <h4 class="text-xs font-bold text-slate-800">Daftar Libur Berikutnya</h4>
                </div>
                <a href="{{ route('harilibur.index') }}" class="text-[10px] font-bold text-emerald-600 hover:underline">Kelola</a>
            </div>

            <div class="space-y-2 text-xs">
                @forelse($upcomingHolidays->skip(1)->take(2) as $hol)
                    <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100/70 transition">
                        <div class="truncate pr-2">
                            <span class="font-bold text-slate-800 block truncate text-[11px]">{{ $hol->keterangan }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ \Carbon\Carbon::parse($hol->tanggal)->translatedFormat('d M Y') }}</span>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 bg-white px-2 py-0.5 rounded-md border border-slate-200 shadow-2xs flex-shrink-0">
                            {{ \Carbon\Carbon::parse($hol->tanggal)->translatedFormat('D') }}
                        </span>
                    </div>
                @empty
                    @if(!$nextHoliday)
                        <div class="text-center py-3 text-slate-400 text-xs">
                            <p class="font-medium">Belum ada jadwal libur</p>
                        </div>
                    @else
                        <div class="p-2.5 rounded-xl bg-slate-50 text-[11px] text-slate-500 border border-slate-100 flex items-center justify-between">
                            <span>Ajukan cuti tahunan</span>
                            <a href="{{ route('pengajuanizin.index') }}" class="font-bold text-emerald-600 hover:underline">Ajukan Cuti</a>
                        </div>
                    @endif
                @endforelse
            </div>

            <!-- Quick Links Footer -->
            <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <a href="{{ route('pengajuanizin.index') }}" class="flex items-center gap-1 font-semibold text-slate-600 hover:text-emerald-700 transition">
                    <i data-lucide="file-text" class="w-3 h-3 text-slate-400"></i>
                    <span>Leave Policy</span>
                </a>
                <a href="{{ route('harilibur.index') }}" class="flex items-center gap-1 font-bold text-emerald-600 hover:underline">
                    <span>Kalender Libur</span>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                </a>
            </div>
        </div>

    </div>

</div>

<!-- ================= ROW 4: TEAM MEMBERS + NOTIFICATIONS + MEETINGS SCHEDULE ================= -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    
    <!-- 1. Statistik Habis Kontrak Karyawan Card (Interactive Tabs V2) -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between" 
         x-data="{ activeContractTab: 'bulanini' }">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white shadow-2xs"
                     style="background: linear-gradient(135deg, #ea580c, #dc2626)">
                    <i data-lucide="file-clock" class="w-3.5 h-3.5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 leading-tight">Masa Habis Kontrak</h3>
                    <span class="text-[10px] text-slate-400">Monitoring Jatuh Tempo</span>
                </div>
            </div>
            @php
                $totalSemuaKontrakAlert = count($kontrak_lewat ?? []) + count($kontrak_bulanini ?? []) + count($kontrak_bulandepan ?? []) + count($kontrak_duabulan ?? []);
            @endphp
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-lg shadow-2xs">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                <span>{{ $totalSemuaKontrakAlert }} Total</span>
            </span>
        </div>

        <!-- 4 Interactive Tabs Pill -->
        <div class="grid grid-cols-4 gap-1 p-1 bg-slate-100 rounded-xl mb-3 text-[11px] font-bold">
            <!-- Tab 1: Lewat Tempo -->
            <button @click="activeContractTab = 'lewat'"
                    :class="activeContractTab === 'lewat' ? 'bg-white text-rose-600 shadow-2xs' : 'text-slate-500 hover:text-slate-800'"
                    class="py-1.5 px-1 rounded-lg transition-all text-center flex flex-col items-center">
                <span class="truncate">Lewat</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full font-black mt-0.5"
                      :class="activeContractTab === 'lewat' ? 'bg-rose-100 text-rose-700' : 'bg-slate-200/70 text-slate-600'">
                    {{ count($kontrak_lewat ?? []) }}
                </span>
            </button>

            <!-- Tab 2: Bulan Ini -->
            <button @click="activeContractTab = 'bulanini'"
                    :class="activeContractTab === 'bulanini' ? 'bg-white text-amber-600 shadow-2xs' : 'text-slate-500 hover:text-slate-800'"
                    class="py-1.5 px-1 rounded-lg transition-all text-center flex flex-col items-center">
                <span class="truncate">Bulan Ini</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full font-black mt-0.5"
                      :class="activeContractTab === 'bulanini' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200/70 text-slate-600'">
                    {{ count($kontrak_bulanini ?? []) }}
                </span>
            </button>

            <!-- Tab 3: Bulan Depan -->
            <button @click="activeContractTab = 'bulandepan'"
                    :class="activeContractTab === 'bulandepan' ? 'bg-white text-yellow-600 shadow-2xs' : 'text-slate-500 hover:text-slate-800'"
                    class="py-1.5 px-1 rounded-lg transition-all text-center flex flex-col items-center">
                <span class="truncate">Bln Depan</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full font-black mt-0.5"
                      :class="activeContractTab === 'bulandepan' ? 'bg-yellow-100 text-yellow-700' : 'bg-slate-200/70 text-slate-600'">
                    {{ count($kontrak_bulandepan ?? []) }}
                </span>
            </button>

            <!-- Tab 4: 2 Bulan Lagi -->
            <button @click="activeContractTab = 'duabulan'"
                    :class="activeContractTab === 'duabulan' ? 'bg-white text-emerald-600 shadow-2xs' : 'text-slate-500 hover:text-slate-800'"
                    class="py-1.5 px-1 rounded-lg transition-all text-center flex flex-col items-center">
                <span class="truncate">2 Bulan</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full font-black mt-0.5"
                      :class="activeContractTab === 'duabulan' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200/70 text-slate-600'">
                    {{ count($kontrak_duabulan ?? []) }}
                </span>
            </button>
        </div>

        <!-- Tab Contents (Max Height with Scroll) -->
        <div class="space-y-2.5 max-h-[210px] overflow-y-auto pr-1 flex-1">
            
            <!-- 1. Lewat Jatuh Tempo List -->
            <div x-show="activeContractTab === 'lewat'" class="space-y-2">
                @forelse($kontrak_lewat ?? [] as $k)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-rose-50/50 border border-rose-100 hover:border-rose-300 transition">
                        <div class="flex items-center gap-2.5 truncate">
                            <div class="w-9 h-9 rounded-full overflow-hidden border border-rose-200 bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                                @if (!empty($k->foto) && Storage::disk('public')->exists('/karyawan/' . $k->foto))
                                    <img src="{{ getfotoKaryawan($k->foto) }}" alt="{{ $k->nama_karyawan }}" class="w-full h-full object-cover">
                                @else
                                    <span>{{ strtoupper(substr($k->nama_karyawan, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="truncate">
                                <h5 class="text-xs font-bold text-slate-900 truncate leading-tight">{{ $k->nama_karyawan }}</h5>
                                <p class="text-[10px] text-slate-500 truncate mt-0.5 font-medium">
                                    <span class="text-slate-700 font-semibold">{{ $k->nama_jabatan ?? '-' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span>{{ $k->nama_dept ?? $k->kode_dept ?? '-' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-slate-400">{{ $k->nama_cabang ?? $k->kode_cabang ?? '-' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 pl-2">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-600 text-white shadow-2xs">Expired</span>
                            <span class="block text-[9px] text-slate-400 mt-0.5 font-mono">{{ date('d M Y', strtotime($k->sampai)) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-xs text-slate-400">
                        <i data-lucide="check-circle" class="w-6 h-6 text-emerald-500 mx-auto mb-1"></i>
                        <span>Tidak ada kontrak yang lewat tempo</span>
                    </div>
                @endforelse
            </div>

            <!-- 2. Bulan Ini List -->
            <div x-show="activeContractTab === 'bulanini'" class="space-y-2">
                @forelse($kontrak_bulanini ?? [] as $k)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50/50 border border-amber-100 hover:border-amber-300 transition">
                        <div class="flex items-center gap-2.5 truncate">
                            <div class="w-9 h-9 rounded-full overflow-hidden border border-amber-200 bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                                @if (!empty($k->foto) && Storage::disk('public')->exists('/karyawan/' . $k->foto))
                                    <img src="{{ getfotoKaryawan($k->foto) }}" alt="{{ $k->nama_karyawan }}" class="w-full h-full object-cover">
                                @else
                                    <span>{{ strtoupper(substr($k->nama_karyawan, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="truncate">
                                <h5 class="text-xs font-bold text-slate-900 truncate leading-tight">{{ $k->nama_karyawan }}</h5>
                                <p class="text-[10px] text-slate-500 truncate mt-0.5 font-medium">
                                    <span class="text-slate-700 font-semibold">{{ $k->nama_jabatan ?? '-' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span>{{ $k->nama_dept ?? $k->kode_dept ?? '-' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-slate-400">{{ $k->nama_cabang ?? $k->kode_cabang ?? '-' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 pl-2">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white shadow-2xs">Bulan Ini</span>
                            <span class="block text-[9px] text-slate-400 mt-0.5 font-mono">{{ date('d M Y', strtotime($k->sampai)) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-xs text-slate-400">
                        <i data-lucide="shield-check" class="w-6 h-6 text-slate-300 mx-auto mb-1"></i>
                        <span>Tidak ada kontrak habis bulan ini</span>
                    </div>
                @endforelse
            </div>

            <!-- 3. Bulan Depan List -->
            <div x-show="activeContractTab === 'bulandepan'" class="space-y-2">
                @forelse($kontrak_bulandepan ?? [] as $k)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-yellow-50/50 border border-yellow-100 hover:border-yellow-300 transition">
                        <div class="flex items-center gap-2.5 truncate">
                            <div class="w-9 h-9 rounded-full overflow-hidden border border-yellow-200 bg-yellow-100 text-yellow-700 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                                @if (!empty($k->foto) && Storage::disk('public')->exists('/karyawan/' . $k->foto))
                                    <img src="{{ getfotoKaryawan($k->foto) }}" alt="{{ $k->nama_karyawan }}" class="w-full h-full object-cover">
                                @else
                                    <span>{{ strtoupper(substr($k->nama_karyawan, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="truncate">
                                <h5 class="text-xs font-bold text-slate-900 truncate leading-tight">{{ $k->nama_karyawan }}</h5>
                                <p class="text-[10px] text-slate-500 truncate mt-0.5 font-medium">
                                    <span class="text-slate-700 font-semibold">{{ $k->nama_jabatan ?? '-' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span>{{ $k->nama_dept ?? $k->kode_dept ?? '-' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-slate-400">{{ $k->nama_cabang ?? $k->kode_cabang ?? '-' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 pl-2">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-500 text-white shadow-2xs">Bln Depan</span>
                            <span class="block text-[9px] text-slate-400 mt-0.5 font-mono">{{ date('d M Y', strtotime($k->sampai)) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-xs text-slate-400">
                        <i data-lucide="shield-check" class="w-6 h-6 text-slate-300 mx-auto mb-1"></i>
                        <span>Tidak ada kontrak habis bulan depan</span>
                    </div>
                @endforelse
            </div>

            <!-- 4. 2 Bulan Lagi List -->
            <div x-show="activeContractTab === 'duabulan'" class="space-y-2">
                @forelse($kontrak_duabulan ?? [] as $k)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50/50 border border-emerald-100 hover:border-emerald-300 transition">
                        <div class="flex items-center gap-2.5 truncate">
                            <div class="w-9 h-9 rounded-full overflow-hidden border border-emerald-200 bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                                @if (!empty($k->foto) && Storage::disk('public')->exists('/karyawan/' . $k->foto))
                                    <img src="{{ getfotoKaryawan($k->foto) }}" alt="{{ $k->nama_karyawan }}" class="w-full h-full object-cover">
                                @else
                                    <span>{{ strtoupper(substr($k->nama_karyawan, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="truncate">
                                <h5 class="text-xs font-bold text-slate-900 truncate leading-tight">{{ $k->nama_karyawan }}</h5>
                                <p class="text-[10px] text-slate-500 truncate mt-0.5 font-medium">
                                    <span class="text-slate-700 font-semibold">{{ $k->nama_jabatan ?? '-' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span>{{ $k->nama_dept ?? $k->kode_dept ?? '-' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-slate-400">{{ $k->nama_cabang ?? $k->kode_cabang ?? '-' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 pl-2">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white shadow-2xs">2 Bulan</span>
                            <span class="block text-[9px] text-slate-400 mt-0.5 font-mono">{{ date('d M Y', strtotime($k->sampai)) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-xs text-slate-400">
                        <i data-lucide="shield-check" class="w-6 h-6 text-slate-300 mx-auto mb-1"></i>
                        <span>Tidak ada kontrak habis 2 bulan lagi</span>
                    </div>
                @endforelse
            </div>

        </div>

        <!-- Footer Action Link -->
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
            <span class="text-[10px] text-slate-400 flex items-center gap-1">
                <i data-lucide="shield-check" class="w-3 h-3 text-emerald-500"></i>
                <span>Sesuai Hak Akses</span>
            </span>
            <a href="{{ route('karyawan.index') }}" class="font-bold text-emerald-600 hover:underline flex items-center gap-0.5">
                <span>Kelola Karyawan</span>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
            </a>
        </div>
    </div>

    <!-- 2. Pengumuman Terbaru Card (Interactive / Dynamic) -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white shadow-2xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }})">
                    <i data-lucide="megaphone" class="w-3.5 h-3.5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 leading-tight">Pengumuman Terbaru</h3>
                    <span class="text-[10px] text-slate-400">Informasi & Surat Edaran</span>
                </div>
            </div>
            <a href="{{ route('pengumuman.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline flex items-center gap-0.5">
                <span>Semua</span>
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <!-- List Pengumuman -->
        <div class="space-y-2.5 max-h-[265px] overflow-y-auto pr-1 my-1 flex-1">
            @forelse($pengumuman_terbaru ?? [] as $item)
                @php
                    $lampiranUrl = $item->lampiran ? Storage::url('pengumuman/' . $item->lampiran) : null;
                @endphp
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200/80 hover:bg-emerald-50/40 hover:border-emerald-200 transition-all duration-200 group cursor-pointer btnOpenDetailPengumuman"
                     data-judul="{{ $item->judul }}"
                     data-isi="{{ $item->isi }}"
                     data-tanggal="{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y, H:i') }}"
                     data-lampiran="{{ $lampiranUrl }}"
                     data-lampiranname="{{ $item->lampiran }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-start gap-2.5 truncate">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100/70 text-emerald-700 flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:bg-emerald-500 group-hover:text-white transition">
                                <i data-lucide="bell" class="w-3.5 h-3.5"></i>
                            </div>
                            <div class="truncate">
                                <h4 class="text-xs font-bold text-slate-800 group-hover:text-emerald-700 transition line-clamp-1 leading-tight">
                                    {{ $item->judul }}
                                </h4>
                                <p class="text-[11px] text-slate-500 line-clamp-2 mt-1 leading-relaxed">
                                    {{ Str::limit(strip_tags($item->isi), 100) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px]">
                        <span class="text-slate-400 flex items-center gap-1 font-mono">
                            <i data-lucide="clock" class="w-3 h-3 text-slate-400"></i>
                            <span>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                        </span>
                        @if ($item->lampiran)
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 font-semibold text-[9px]">
                                <i data-lucide="paperclip" class="w-2.5 h-2.5"></i>
                                <span>Lampiran</span>
                            </span>
                        @else
                            <span class="text-slate-400 font-medium text-[10px] group-hover:text-emerald-600 transition">Baca Detail &rarr;</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400">
                    <i data-lucide="megaphone-off" class="w-8 h-8 mx-auto mb-1.5 text-slate-300"></i>
                    <p class="text-xs font-medium">Belum ada pengumuman terbaru</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
            <span class="flex items-center gap-1">
                <i data-lucide="info" class="w-3.5 h-3.5 text-emerald-500"></i>
                <span>Broadcast Karyawan</span>
            </span>
            <span class="text-[10px] font-bold text-slate-400">{{ count($pengumuman_terbaru ?? []) }} Diterbitkan</span>
        </div>
    </div>

    <!-- 3. Karyawan Terlambat Hari Ini Card (Interactive & Real-time) -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white shadow-2xs"
                     style="background: linear-gradient(135deg, #f43f5e, #e11d48)">
                    <i data-lucide="clock-alert" class="w-3.5 h-3.5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 leading-tight">Terlambat Hari Ini</h3>
                    <span class="text-[10px] text-slate-400">Monitoring Presensi Masuk</span>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-lg shadow-2xs">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                <span>{{ count($karyawan_terlambat_hariini ?? []) }} Karyawan</span>
            </span>
        </div>

        <!-- List Karyawan Terlambat -->
        <div class="space-y-2.5 max-h-[265px] overflow-y-auto pr-1 my-1 flex-1">
            @forelse($karyawan_terlambat_hariini ?? [] as $item)
                @php
                    $jamMasuk = \Carbon\Carbon::parse($item->jam_in);
                    $jadwal = \Carbon\Carbon::parse($item->tanggal . ' ' . $item->jadwal_masuk);
                    $menitTerlambat = $jamMasuk->diffInMinutes($jadwal);
                    $jamTerlambat = floor($menitTerlambat / 60);
                    $sisaMenit = $menitTerlambat % 60;
                    
                    $durasiTerlambatText = '';
                    if ($jamTerlambat > 0) {
                        $durasiTerlambatText .= $jamTerlambat . 'j ';
                    }
                    $durasiTerlambatText .= $sisaMenit . 'm';
                @endphp
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-rose-50/40 border border-rose-100/90 hover:border-rose-300 hover:bg-rose-50/70 transition-all duration-200 group">
                    <div class="flex items-center gap-2.5 truncate">
                        <!-- Avatar Foto -->
                        <div class="w-9 h-9 rounded-full overflow-hidden border border-rose-200 bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                            @if (!empty($item->foto) && Storage::disk('public')->exists('/karyawan/' . $item->foto))
                                <img src="{{ getfotoKaryawan($item->foto) }}" alt="{{ $item->nama_karyawan }}" class="w-full h-full object-cover">
                            @else
                                <span>{{ strtoupper(substr($item->nama_karyawan, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="truncate">
                            <h5 class="text-xs font-bold text-slate-900 truncate leading-tight group-hover:text-rose-600 transition-colors">
                                {{ $item->nama_karyawan }}
                            </h5>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5 font-medium">
                                <span class="text-slate-700 font-semibold">{{ $item->nama_jabatan ?? '-' }}</span>
                                <span class="text-slate-300">•</span>
                                <span>{{ $item->nama_dept ?? '-' }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="text-slate-400">{{ $item->nama_cabang ?? '-' }}</span>
                            </p>
                        </div>
                    </div>
                    <!-- Waktu Masuk & Badge Keterlambatan -->
                    <div class="text-right flex-shrink-0 pl-2">
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white shadow-2xs font-mono">
                            +{{ $durasiTerlambatText }}
                        </span>
                        <span class="block text-[9px] text-slate-500 mt-0.5 font-mono">
                            {{ date('H:i', strtotime($item->jam_in)) }} WIB
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2 shadow-2xs">
                        <i data-lucide="check-check" class="w-5 h-5"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-700">Semua Tepat Waktu</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Tidak ada karyawan yang terlambat hari ini</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
            <span class="flex items-center gap-1">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
                <span>Sesuai Hak Akses</span>
            </span>
            <a href="{{ route('presensi.index') }}" class="text-[10px] font-bold text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-0.5">
                <span>Rekap Presensi</span>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
            </a>
        </div>
    </div>

</div>

<!-- Modal Detail Karyawan Presensi V2 (Consistent V2 System) -->
<div class="modal fade" id="modalKaryawanPresensi" tabindex="-1" aria-labelledby="modalKaryawanPresensiLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
            <!-- Consistent V2 Modal Header -->
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-users"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalKaryawanPresensiLabel">
                            Detail Kehadiran Karyawan
                        </h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium">
                            Daftar presensi dan rincian kehadiran karyawan
                        </p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <!-- Modal Body Container -->
            <div class="p-6 overflow-y-auto" id="modalKaryawanPresensiContent">
                <!-- Data loaded via Ajax -->
                <div class="text-center py-12">
                    <div class="inline-block w-8 h-8 border-4 border-slate-200 border-t-emerald-600 rounded-full animate-spin"></div>
                    <div class="text-xs text-slate-400 font-semibold mt-3">Memuat data kehadiran...</div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                <button type="button" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL PENGUMUMAN DASHBOARD V2 ================= -->
<div id="modalDetailPengumumanDashboard" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i data-lucide="megaphone" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="dashDetailJudul">
                        Detail Pengumuman
                    </h3>
                    <p class="text-[11px] text-slate-500" id="dashDetailTanggal">
                        -
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseDashDetailModal w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Body Content -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4">
            <!-- Lampiran Area (Conditional) -->
            <div id="dashDetailLampiranArea" class="hidden">
                <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <i data-lucide="paperclip" class="w-4 h-4"></i>
                        </div>
                        <div class="truncate">
                            <span class="block text-xs font-bold text-slate-800 truncate" id="dashDetailLampiranName">Lampiran Dokumen</span>
                            <span class="block text-[10px] text-slate-400">Klik untuk membuka lampiran</span>
                        </div>
                    </div>
                    <a href="#" id="dashDetailLampiranLink" target="_blank" 
                       class="px-3 py-1.5 bg-white border border-slate-200 hover:border-emerald-500 hover:text-emerald-700 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition shrink-0 inline-flex items-center gap-1">
                        <span>Buka</span>
                        <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <!-- Isi Pengumuman Text -->
            <div class="space-y-1">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Isi Informasi</label>
                <div id="dashDetailIsi" class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50/50 p-4 rounded-2xl border border-slate-100 min-h-[120px]">
                    -
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-5 sm:px-6 py-3 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
            <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
                <span>Informasi Resmi Perusahaan</span>
            </span>
            <button type="button" class="btnCloseDashDetailModal px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 text-xs font-bold transition shadow-2xs cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<!-- Chart.js CDN for Leave & Performance Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ==========================================
// 1. REALTIME DIGITAL CLOCK & SECONDS RING ANIMATION (Runs IMMEDIATELY)
// ==========================================
(function initLiveClockAndSeconds() {
    const radius = 46;
    const circumference = 2 * Math.PI * radius; // 289.026

    function updateLiveClock() {
        const ring = document.getElementById('secondsProgressRing');
        const secondsOnlyEl = document.getElementById('smartSecondsOnly');
        const clockEl = document.getElementById('smartClock');
        const fullDateEl = document.getElementById('liveFullDate');

        const now = new Date();
        
        // Time Components
        const hours24 = now.getHours();
        const hours12 = hours24 % 12 || 12;
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const secondsNum = now.getSeconds();
        const secondsStr = String(secondsNum).padStart(2, '0');
        const ampm = hours24 >= 12 ? 'PM' : 'AM';

        // 1. Update Big Live Seconds inside circle
        if (secondsOnlyEl) secondsOnlyEl.textContent = secondsStr;

        // 2. Update Digital Clock
        if (clockEl) clockEl.textContent = `${String(hours12).padStart(2, '0')}:${minutes} ${ampm}`;

        // 3. Smooth Continuous Circular Rotation of Seconds Ring
        if (ring) {
            const currentSeconds = secondsNum + (now.getMilliseconds() / 1000);
            const progress = currentSeconds / 60; // 0 to 1
            const offset = circumference * (1 - progress);
            ring.style.strokeDashoffset = offset;
        }

        // 4. Update Header Date Text
        if (fullDateEl) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const day = String(now.getDate()).padStart(2, '0');
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            fullDateEl.textContent = `${String(hours12).padStart(2, '0')}:${minutes} ${ampm}, ${day} ${month} ${year}`;
        }
    }

    // Run right away and continuously every 60ms for ultra-smooth 60fps rotation
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            updateLiveClock();
            setInterval(updateLiveClock, 60);
        });
    } else {
        updateLiveClock();
        setInterval(updateLiveClock, 60);
    }
})();

// ==========================================
// 2. CHARTS INITIALIZATION (Safely wrapped)
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
    // Modal Stat Card Click Event
    $(document).on('click', '.stat-card-clickable', function() {
        var status = $(this).data('status');
        if (!status) return;

        var urlParams = new URLSearchParams(window.location.search);
        var tanggal = urlParams.get('tanggal') || '{{ Request("tanggal") }}' || '';
        var kode_cabang = urlParams.get('kode_cabang') || '{{ Request("kode_cabang") }}' || '';
        var kode_dept = urlParams.get('kode_dept') || '{{ Request("kode_dept") }}' || '';

        // Tampilkan spinner loading
        $('#modalKaryawanPresensiContent').html(`
            <div class="text-center py-10">
                <div class="inline-block w-8 h-8 border-4 border-slate-200 border-t-emerald-600 rounded-full animate-spin"></div>
                <div class="text-xs text-slate-400 font-semibold mt-3">Memuat data presensi...</div>
            </div>
        `);
        
        $('#modalKaryawanPresensiLabel').text('Detail Kehadiran Karyawan');
        $('#modalKaryawanPresensi').modal('show');

        $.ajax({
            type: 'POST',
            url: '{{ route("dashboard.get.karyawan.presensi") }}',
            data: {
                _token: '{{ csrf_token() }}',
                status: status,
                tanggal: tanggal,
                kode_cabang: kode_cabang,
                kode_dept: kode_dept
            },
            success: function(response) {
                if(response.success) {
                    $('#modalKaryawanPresensiLabel').text(response.title);
                    $('#modalKaryawanPresensiContent').html(response.html);
                    if (window.lucide) lucide.createIcons();
                } else {
                    $('#modalKaryawanPresensiContent').html(`<div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs">Gagal memuat data presensi.</div>`);
                }
            },
            error: function(xhr, status, error) {
                $('#modalKaryawanPresensiContent').html(`<div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs">Terjadi kesalahan: ${error}</div>`);
            }
        });
    });

    // Realtime live search inside the modal
    $(document).on('keyup', '#searchKaryawanNama', function() {
        var value = $(this).val().toLowerCase();
        $("#modalKaryawanPresensiContent table tbody tr").filter(function() {
            var rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.indexOf(value) > -1);
        });
    });

    if (typeof Chart === 'undefined') return;

    // 1. Statistik Pendidikan Karyawan Donut Chart
    const pddCtx = document.getElementById('smartPendidikanChart')?.getContext('2d');
    if (pddCtx) {
        const pddLabels = {!! json_encode(array_values(array_map(fn($item) => $item['label'], $filteredPendidikan))) !!};
        const pddData = {!! json_encode(array_values(array_map(fn($item) => $item['count'], $filteredPendidikan))) !!};
        const pddColors = {!! json_encode(array_values(array_map(fn($item) => $item['color'], $filteredPendidikan))) !!};

        new Chart(pddCtx, {
            type: 'doughnut',
            data: {
                labels: pddLabels,
                datasets: [{
                    data: pddData,
                    backgroundColor: pddColors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.parsed || 0;
                                const total = {{ $totalKaryawanPendidikan > 0 ? $totalKaryawanPendidikan : 1 }};
                                const pct = Math.round((val / total) * 100);
                                return ` ${context.label}: ${val} orang (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Performance Monthly Attendance Bar Chart (Tepat Waktu vs Terlambat)
    const perfCtx = document.getElementById('smartPerformanceChart')?.getContext('2d');
    if (perfCtx) {
        const perfLabels = {!! json_encode($perfChartLabels) !!};
        const dataTepatWaktu = {!! json_encode($perfChartTepatWaktu) !!};
        const dataTerlambat = {!! json_encode($perfChartTerlambat) !!};

        new Chart(perfCtx, {
            type: 'bar',
            data: {
                labels: perfLabels,
                datasets: [
                    {
                        label: 'Tepat Waktu',
                        data: dataTepatWaktu,
                        backgroundColor: '#10b981',
                        hoverBackgroundColor: '#059669',
                        borderRadius: 4,
                        borderSkipped: false,
                        barPercentage: 0.8,
                        categoryPercentage: 0.7
                    },
                    {
                        label: 'Terlambat',
                        data: dataTerlambat,
                        backgroundColor: '#f43f5e',
                        hoverBackgroundColor: '#e11d48',
                        borderRadius: 4,
                        borderSkipped: false,
                        barPercentage: 0.8,
                        categoryPercentage: 0.7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        padding: 9,
                        cornerRadius: 8,
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 11 },
                        callbacks: {
                            title: function(context) {
                                return `Tgl ${context[0].label} {{ \Carbon\Carbon::now(config('app.timezone'))->translatedFormat('F Y') }}`;
                            },
                            label: function(context) {
                                return ` ${context.dataset.label}: ${context.parsed.y} karyawan`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            font: { size: 9 },
                            color: '#94a3b8',
                            maxTicksLimit: 16
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(241, 245, 249, 0.9)',
                            borderDash: [3, 3]
                        },
                        border: { display: false },
                        ticks: {
                            font: { size: 9 },
                            color: '#94a3b8',
                            precision: 0
                        }
                    }
                }
            }
        });
    }
});

function kirimUcapanSemuaV2() {
    const btn = document.getElementById('btnKirimUcapanV2');
    const btnText = document.getElementById('btnTextV2');

    if (btn) btn.disabled = true;
    if (btnText) btnText.textContent = 'Mengirim...';

    const urlParams = new URLSearchParams(window.location.search);
    const kodeCabang = urlParams.get('kode_cabang') || '';
    const kodeDept = urlParams.get('kode_dept') || '';

    fetch('{{ route('dashboard.kirim.ucapan.birthday') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            kode_cabang: kodeCabang,
            kode_dept: kodeDept
        })
    })
    .then(response => response.json())
    .then(data => {
        if (btn) btn.disabled = false;
        if (btnText) btnText.textContent = 'Kirim Ucapan WA';

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Ucapan ulang tahun berhasil dikirim via WhatsApp',
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message || 'Gagal mengirim ucapan ulang tahun',
            });
        }
    })
    .catch(error => {
        if (btn) btn.disabled = false;
        if (btnText) btnText.textContent = 'Kirim Ucapan WA';

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan sistem saat mengirim ucapan.',
        });
    });
}

// ==========================================
// PENGUMUMAN DETAIL MODAL HANDLER DASHBOARD V2
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalDetailPengumumanDashboard');
    const judulEl = document.getElementById('dashDetailJudul');
    const tglEl = document.getElementById('dashDetailTanggal');
    const isiEl = document.getElementById('dashDetailIsi');
    const lampiranArea = document.getElementById('dashDetailLampiranArea');
    const lampiranLink = document.getElementById('dashDetailLampiranLink');
    const lampiranName = document.getElementById('dashDetailLampiranName');

    document.querySelectorAll('.btnOpenDetailPengumuman').forEach(btn => {
        btn.addEventListener('click', function () {
            const judul = this.getAttribute('data-judul');
            const isi = this.getAttribute('data-isi');
            const tanggal = this.getAttribute('data-tanggal');
            const lampiran = this.getAttribute('data-lampiran');
            const lampiranFilename = this.getAttribute('data-lampiranname');

            if (judulEl) judulEl.textContent = judul;
            if (tglEl) tglEl.textContent = 'Diterbitkan: ' + tanggal;
            if (isiEl) isiEl.textContent = isi;

            if (lampiran && lampiran !== 'null' && lampiran !== '') {
                if (lampiranLink) lampiranLink.setAttribute('href', lampiran);
                if (lampiranName) lampiranName.textContent = lampiranFilename || 'Lampiran Dokumen';
                if (lampiranArea) lampiranArea.classList.remove('hidden');
            } else {
                if (lampiranArea) lampiranArea.classList.add('hidden');
            }

            if (modal) {
                modal.classList.remove('hidden');
                if (window.lucide) lucide.createIcons();
            }
        });
    });

    document.querySelectorAll('.btnCloseDashDetailModal').forEach(btn => {
        btn.addEventListener('click', function () {
            if (modal) modal.classList.add('hidden');
        });
    });

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
