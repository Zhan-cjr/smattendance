@extends('layouts.app')
@section('titlepage', 'Dashboard Overview')

@section('content')
@section('navigasi')
    <span>Dashboard Overview</span>
@endsection

@php
    $authUser = auth()->user();
    $fullName = $authUser->name ?? 'Pengguna';
    $userName = explode(' ', $fullName)[0];
    $currentHour = (int) date('H');

    if ($currentHour >= 5 && $currentHour < 12) {
        $greeting = 'Selamat Pagi';
        $greetIcon = 'ti-sun';
    } elseif ($currentHour >= 12 && $currentHour < 15) {
        $greeting = 'Selamat Siang';
        $greetIcon = 'ti-sun-high';
    } elseif ($currentHour >= 15 && $currentHour < 19) {
        $greeting = 'Selamat Sore';
        $greetIcon = 'ti-sunset';
    } else {
        $greeting = 'Selamat Malam';
        $greetIcon = 'ti-moon-stars';
    }

    $targetDateObj = \Carbon\Carbon::parse($target_tanggal ?? date('Y-m-d'));
    $isToday = $targetDateObj->isToday();
    $tanggalTampil = getnamaHari($targetDateObj->format('D')) . ', ' . DateToIndo($targetDateObj->format('Y-m-d'));

    $total_aktif = $status_karyawan->jml_aktif ?? 0;
    $hadir = (int) ($rekappresensi->hadir ?? 0);
    $tepat_waktu = (int) ($rekappresensi->tepat_waktu ?? 0);
    $terlambat = (int) ($rekappresensi->terlambat ?? 0);
    $izin = (int) ($rekappresensi->izin ?? 0);
    $sakit = (int) ($rekappresensi->sakit ?? 0);
    $cuti = (int) ($rekappresensi->cuti ?? 0);
    $alpa = (int) ($rekappresensi->alpa ?? 0);
    $izin_total = $izin + $sakit + $cuti;
    $belum_absen = max(0, $total_aktif - ($hadir + $izin_total + $alpa));
    $persen_kehadiran = $total_aktif > 0 ? round(($hadir / $total_aktif) * 100, 1) : 0;
@endphp

<style>
    /* Dashboard Container Spacing */
    .dashboard-header-bar {
        margin-top: 0.5rem;
        margin-bottom: 1.25rem;
    }

    /* Hero Banner Modern Styling */
    .bento-hero {
        background: linear-gradient(135deg, var(--theme-color-1) 0%, var(--theme-color-2) 100%);
        border-radius: 20px;
        padding: 2rem 2.25rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 14px 32px -8px rgba(var(--theme-color-1-rgb), 0.35);
        border: 1px solid rgba(255, 255, 255, 0.15);
        margin-bottom: 1.5rem;
    }

    .bento-hero::before {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0) 70%);
        top: -100px;
        right: -50px;
        pointer-events: none;
    }

    .bento-hero::after {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
        bottom: -80px;
        left: 20%;
        pointer-events: none;
    }

    /* Transparent Badges & Cards Inside Hero */
    .glass-banner-badge {
        background: rgba(255, 255, 255, 0.18) !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 9999px;
        padding: 0.45rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .glass-banner-clock {
        background: rgba(0, 0, 0, 0.18) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        color: #ffffff !important;
        display: inline-flex;
        flex-direction: column;
    }

    /* 5-Card Uniform Bento Grid */
    .bento-stats-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 1200px) {
        .bento-stats-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .bento-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 520px) {
        .bento-stats-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Individual Stat Card */
    .bento-stat-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 1.25rem 1.35rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 6px 18px -2px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 155px;
        height: 100%;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .bento-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px -4px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }

    .bento-stat-card.hero-stat-card {
        background: linear-gradient(135deg, var(--theme-color-1) 0%, var(--theme-color-2) 100%);
        color: #ffffff;
        border: none;
        box-shadow: 0 10px 24px -4px rgba(var(--theme-color-1-rgb), 0.35);
    }

    .bento-stat-card.hero-stat-card .stat-title {
        color: rgba(255, 255, 255, 0.85);
    }

    .bento-stat-card.hero-stat-card .stat-num {
        color: #ffffff;
    }

    .bento-stat-card.hero-stat-card .stat-subtitle {
        color: rgba(255, 255, 255, 0.85);
    }

    .stat-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: transform 0.2s ease;
        flex-shrink: 0;
    }

    .bento-stat-card:hover .stat-icon-wrapper {
        transform: scale(1.08);
    }

    .stat-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 0.35rem;
    }

    .stat-num {
        font-size: 1.95rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 0.4rem;
        color: #0f172a;
    }

    .stat-subtitle {
        font-size: 0.8rem;
        color: #94a3b8;
        font-weight: 500;
        line-height: 1.3;
    }

    /* Progress Bar in First Stat Card */
    .rate-progress-bar {
        height: 5px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.25);
        overflow: hidden;
        margin-top: 0.4rem;
    }

    .rate-progress-fill {
        height: 100%;
        border-radius: 999px;
        background: #ffffff;
        transition: width 0.8s ease-in-out;
    }

    /* Pill Tabs for Contract */
    .nav-pills-bento .nav-link {
        border-radius: 12px;
        padding: 0.5rem 0.9rem;
        font-weight: 600;
        font-size: 0.825rem;
        color: #64748b;
        background: #f1f5f9;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .nav-pills-bento .nav-link.active {
        background: #0f172a;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
    }

    .hover-elevate {
        transition: all 0.2s ease;
    }

    .hover-elevate:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px -3px rgba(15, 23, 42, 0.08);
    }

    /* Live Feed Avatar */
    .feed-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        object-fit: cover;
    }
</style>

<!-- Top Control & Filter Row -->
<div class="dashboard-header-bar d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="badge bg-label-primary px-3 py-2 fs-6 rounded-pill d-inline-flex align-items-center gap-2 shadow-xs">
            <i class="ti ti-activity-heartbeat fs-5"></i>
            <span>{{ $isToday ? 'Monitoring Hari Ini' : 'Data Tanggal: ' . formatIndo($target_tanggal) }}</span>
        </span>

        @if(Request('tanggal') || Request('kode_cabang') || Request('kode_dept'))
            <div class="d-inline-flex align-items-center gap-1.5 flex-wrap">
                @if(Request('tanggal'))
                    <span class="badge bg-white text-dark border px-2.5 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1">
                        <i class="ti ti-calendar text-primary"></i> {{ formatIndo(Request('tanggal')) }}
                    </span>
                @endif
                @if(Request('kode_cabang'))
                    <span class="badge bg-white text-dark border px-2.5 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1">
                        <i class="ti ti-building text-primary"></i> Cabang: {{ textUpperCase(Request('kode_cabang')) }}
                    </span>
                @endif
                @if(Request('kode_dept'))
                    <span class="badge bg-white text-dark border px-2.5 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1">
                        <i class="ti ti-folders text-primary"></i> Dept: {{ textUpperCase(Request('kode_dept')) }}
                    </span>
                @endif
                <a href="{{ route('dashboard.index') }}" class="badge bg-label-danger px-2.5 py-1.5 rounded-pill text-decoration-none" title="Reset Semua Filter">
                    <i class="ti ti-x"></i> Reset
                </a>
            </div>
        @endif
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('monitoring-lokasi.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1.5 shadow-xs">
            <i class="ti ti-map-pin"></i>
            <span>Live Map</span>
        </a>
        <a href="{{ route('presensi.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1.5 shadow-xs">
            <i class="ti ti-list-check"></i>
            <span>Data Presensi</span>
        </a>
        <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1.5 shadow-xs" data-bs-toggle="modal" data-bs-target="#filterDashboardModal">
            <i class="ti ti-adjustments-horizontal"></i>
            <span>Filter Data</span>
            @if(Request('tanggal') || Request('kode_cabang') || Request('kode_dept'))
                <span class="badge bg-white text-primary rounded-pill ms-1">Aktif</span>
            @endif
        </button>
    </div>
</div>

<!-- Modal Filter -->
<div class="modal fade" id="filterDashboardModal" tabindex="-1" aria-labelledby="filterDashboardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="filterDashboardModalLabel">
                    <i class="ti ti-filter me-2 text-primary"></i>Filter Kehadiran & Data Karyawan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.index') }}" method="GET">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <x-input-with-icon label="Tanggal Presensi" icon="ti ti-calendar" name="tanggal" datepicker="flatpickr-date"
                                value="{{ Request('tanggal', $target_tanggal) }}" />
                        </div>
                        <div class="col-12">
                            <x-select label="Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                selected="{{ Request('kode_cabang') }}" />
                        </div>
                        <div class="col-12">
                            <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                                selected="{{ Request('kode_dept') }}" upperCase="true" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary">Reset</a>
                    <button class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <i class="ti ti-search"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hero Welcome Banner -->
<div class="bento-hero">
    <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-lg-8">
            <div class="glass-banner-badge mb-2">
                <i class="ti {{ $greetIcon }} fs-6"></i>
                <span>{{ $greeting }}, {{ $userName }} 👋</span>
            </div>
            <h2 class="text-white fw-bold mb-2" style="letter-spacing: -0.5px;">Dashboard Presensi Terpadu</h2>
            <p class="mb-3 text-white text-opacity-80 fs-6" style="max-width: 620px;">
                Pantau kehadiran realtime, kedisiplinan jam kerja, masa berlaku kontrak, dan demografi karyawan secara terpusat.
            </p>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="glass-banner-badge">
                    <i class="ti ti-users"></i>
                    <span>{{ $total_aktif }} Karyawan Aktif</span>
                </div>
                <div class="glass-banner-badge">
                    <i class="ti ti-user-check"></i>
                    <span>{{ $hadir }} Hadir Hari Ini ({{ $persen_kehadiran }}%)</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
            <div class="glass-banner-clock">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="ti ti-calendar-event fs-5 text-white"></i>
                    <span class="fw-bold text-white fs-6">{{ $tanggalTampil }}</span>
                </div>
                <div class="d-flex align-items-center gap-2 text-white mb-1">
                    <i class="ti ti-clock fs-5"></i>
                    <span class="fw-bold fs-5" id="realtimeClock">{{ date('H:i:s') }}</span>
                    <span class="badge px-2 py-0.5 rounded-pill fs-8" style="background: rgba(255, 255, 255, 0.2); color: #fff;">WIB</span>
                </div>
                <div class="text-white text-opacity-70 fs-8">
                    Zona Waktu: {{ config('app.timezone', 'Asia/Jakarta') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5-Card Uniform Bento Grid -->
<div class="bento-stats-grid">
    <!-- Card 1: Tingkat Kehadiran -->
    <div class="bento-stat-card hero-stat-card">
        <div>
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-title">Tingkat Kehadiran</div>
                    <div class="stat-num">{{ $persen_kehadiran }}%</div>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(255, 255, 255, 0.2); color: #ffffff;">
                    <i class="ti ti-chart-donut-4"></i>
                </div>
            </div>
            <div class="rate-progress-bar">
                <div class="rate-progress-fill" style="width: {{ min(100, $persen_kehadiran) }}%;"></div>
            </div>
        </div>
        <div class="stat-subtitle mt-2">
            <span>{{ $hadir }} dari {{ $total_aktif }} Karyawan</span>
        </div>
    </div>

    <!-- Card 2: Hadir Tepat Waktu -->
    <div class="bento-stat-card">
        <div class="d-flex align-items-start justify-content-between">
            <div>
                <div class="stat-title">Hadir Tepat Waktu</div>
                <div class="stat-num text-success">{{ $tepat_waktu }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="ti ti-clock-check"></i>
            </div>
        </div>
        <div class="stat-subtitle d-flex align-items-center gap-1 text-success">
            <i class="ti ti-circle-check fs-6"></i>
            <span>Disiplin Masuk</span>
        </div>
    </div>

    <!-- Card 3: Terlambat -->
    <div class="bento-stat-card">
        <div class="d-flex align-items-start justify-content-between">
            <div>
                <div class="stat-title">Terlambat</div>
                <div class="stat-num text-warning">{{ $terlambat }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                <i class="ti ti-clock-exclamation"></i>
            </div>
        </div>
        <div class="stat-subtitle d-flex align-items-center gap-1 text-warning">
            <i class="ti ti-alert-triangle fs-6"></i>
            <span>Lewat Jam Masuk</span>
        </div>
    </div>

    <!-- Card 4: Izin, Sakit & Cuti -->
    <div class="bento-stat-card">
        <div class="d-flex align-items-start justify-content-between">
            <div>
                <div class="stat-title">Izin & Cuti</div>
                <div class="stat-num text-info">{{ $izin_total }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(14, 165, 233, 0.12); color: #0284c7;">
                <i class="ti ti-file-certificate"></i>
            </div>
        </div>
        <div class="stat-subtitle text-muted">
            <span>I: {{ $izin }} &bull; S: {{ $sakit }} &bull; C: {{ $cuti }}</span>
        </div>
    </div>

    <!-- Card 5: Belum Absen / Alpa -->
    <div class="bento-stat-card">
        <div class="d-flex align-items-start justify-content-between">
            <div>
                <div class="stat-title">Belum Absen / Alpa</div>
                <div class="stat-num text-danger">{{ $belum_absen + $alpa }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="ti ti-user-x"></i>
            </div>
        </div>
        <div class="stat-subtitle d-flex align-items-center gap-1 text-danger">
            <i class="ti ti-point-filled fs-6"></i>
            <span>Alpa: {{ $alpa }} | Belum: {{ $belum_absen }}</span>
        </div>
    </div>
</div>

<!-- Employee Status Summary Strip -->
<div class="card mb-4">
    <div class="card-body p-3.5">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-3 border-end-md">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-label-primary rounded-circle" style="width: 48px; height: 48px;">
                        <i class="ti ti-users-group fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-7 text-uppercase fw-bold">Total Karyawan Aktif</span>
                        <h4 class="fw-bold mb-0 text-slate-800">{{ $status_karyawan->jml_aktif ?? 0 }} <span class="fs-7 text-muted fw-normal">Orang</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-9">
                <div class="row g-2.5">
                    @forelse ($status_karyawan->rekap_status as $rekap)
                        <div class="col-6 col-sm-4 col-lg-3">
                            <div class="p-2.5 rounded-3 bg-slate-50 border border-slate-100 hover-elevate">
                                <span class="d-block text-muted fs-8 text-truncate mb-0.5">{{ $rekap->nama_status_karyawan }}</span>
                                <h5 class="fw-bold mb-0 text-dark">{{ $rekap->total }} <span class="fs-8 text-muted fw-normal">Org</span></h5>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-muted fs-7">Tidak ada data status karyawan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main 2-Column Content -->
<div class="row g-4">
    <!-- Left Column (8 cols): Realtime Feed, Contracts, Birthday -->
    <div class="col-12 col-lg-8">

        <!-- 1. Real-Time Attendance Stream (Presensi Terkini) -->
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle">
                        <i class="ti ti-fingerprint fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Presensi Masuk Terkini</h5>
                        <small class="text-muted">Aktivitas kehadiran karyawan yang tercatat pada tanggal terpilih</small>
                    </div>
                </div>
                <a href="{{ route('presensi.index') }}" class="btn btn-light btn-sm rounded-pill d-inline-flex align-items-center gap-1 px-3">
                    <span>Lihat Semua</span>
                    <i class="ti ti-arrow-right fs-7"></i>
                </a>
            </div>
            <div class="card-body">
                @if(isset($presensi_terbaru) && count($presensi_terbaru) > 0)
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Karyawan</th>
                                    <th>Dept & Cabang</th>
                                    <th>Jam Kerja</th>
                                    <th>Jam Masuk</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($presensi_terbaru as $p)
                                    @php
                                        $terlambatInfo = hitungjamterlambat($p->jam_in, $p->jam_masuk);
                                        $isLate = !empty($terlambatInfo) && $terlambatInfo['desimal_terlambat'] > 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2.5">
                                                @if(!empty($p->foto) && Storage::disk('public')->exists('/karyawan/' . $p->foto))
                                                    <img src="{{ getfotoKaryawan($p->foto) }}" alt="{{ $p->nama_karyawan }}" class="feed-avatar shadow-xs border">
                                                @else
                                                    <div class="feed-avatar bg-label-primary d-flex align-items-center justify-content-center fw-bold border">
                                                        {{ strtoupper(substr($p->nama_karyawan, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold text-dark">{{ formatName($p->nama_karyawan) }}</div>
                                                    <small class="text-muted">NIK: {{ $p->nik }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $p->nama_dept }}</div>
                                            <small class="text-muted">{{ textUpperCase($p->kode_cabang) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-secondary">{{ $p->nama_jam_kerja ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                                <i class="ti ti-clock fs-7 text-muted"></i>
                                                {{ !empty($p->jam_in) ? date('H:i', strtotime($p->jam_in)) : '-' }}
                                            </div>
                                            @if(!empty($p->jam_out))
                                                <small class="text-muted">Out: {{ date('H:i', strtotime($p->jam_out)) }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($isLate)
                                                <span class="badge bg-label-danger rounded-pill d-inline-flex align-items-center gap-1">
                                                    <i class="ti ti-alert-triangle fs-8"></i> Telat {{ $terlambatInfo['menitterlambat'] }} mnt
                                                </span>
                                            @else
                                                <span class="badge bg-label-success rounded-pill d-inline-flex align-items-center gap-1">
                                                    <i class="ti ti-check fs-8"></i> Tepat Waktu
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="avatar avatar-lg bg-label-secondary mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="ti ti-fingerprint-off fs-2 text-muted"></i>
                        </div>
                        <h6 class="fw-bold text-muted mb-1">Belum Ada Presensi Masuk</h6>
                        <p class="text-muted fs-7 mb-0">Aktivitas scan presensi karyawan hari ini akan otomatis muncul di sini.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. Contract Expiration Tabs Widget -->
        @php
            $contractTabs = [
                [
                    'id' => 'bulanini',
                    'label' => 'Bulan Ini',
                    'badge_class' => 'bg-danger',
                    'icon' => 'ti-calendar-exclamation',
                    'items' => $kontrak_bulanini,
                    'active' => true,
                ],
                [
                    'id' => 'lewatjatuhtempo',
                    'label' => 'Lewat Tempo',
                    'badge_class' => 'bg-danger',
                    'icon' => 'ti-alert-octagon',
                    'items' => $kontrak_lewat,
                    'active' => false,
                ],
                [
                    'id' => 'bulandepan',
                    'label' => 'Bulan Depan',
                    'badge_class' => 'bg-warning',
                    'icon' => 'ti-calendar-stats',
                    'items' => $kontrak_bulandepan,
                    'active' => false,
                ],
                [
                    'id' => 'duabulan',
                    'label' => '2 Bulan Lagi',
                    'badge_class' => 'bg-success',
                    'icon' => 'ti-calendar-time',
                    'items' => $kontrak_duabulan,
                    'active' => false,
                ],
            ];
            $totalSemuaKontrak = count($kontrak_lewat) + count($kontrak_bulanini) + count($kontrak_bulandepan) + count($kontrak_duabulan);
        @endphp

        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-danger bg-opacity-15 text-danger rounded-circle">
                        <i class="ti ti-file-certificate fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Monitoring Kontrak Kerja</h5>
                        <small class="text-muted">Pantau masa berlaku kontrak PKWT yang segera atau sudah berakhir</small>
                    </div>
                </div>
                <span class="badge bg-label-danger rounded-pill px-3 py-2 fs-7 fw-bold">
                    {{ $totalSemuaKontrak }} Kontrak Dipantau
                </span>
            </div>
            <div class="card-body">
                <!-- Nav Pills -->
                <ul class="nav nav-pills nav-pills-bento mb-3" id="contractTab" role="tablist">
                    @foreach ($contractTabs as $tab)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab['active'] ? 'active' : '' }} d-inline-flex align-items-center gap-2" id="{{ $tab['id'] }}-tab" data-bs-toggle="pill" data-bs-target="#{{ $tab['id'] }}" type="button" role="tab">
                                <i class="ti {{ $tab['icon'] }}"></i>
                                <span>{{ $tab['label'] }}</span>
                                <span class="badge {{ $tab['badge_class'] }} rounded-pill ms-1">{{ count($tab['items']) }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab Content -->
                <div class="tab-content border-0 p-0" id="contractTabContent">
                    @foreach ($contractTabs as $tab)
                        <div class="tab-pane fade {{ $tab['active'] ? 'show active' : '' }}" id="{{ $tab['id'] }}" role="tabpanel">
                            @if (count($tab['items']) === 0)
                                <div class="text-center py-4 text-muted">
                                    <i class="ti ti-circle-check fs-2 text-success mb-2 d-block"></i>
                                    Tidak ada data kontrak pada kategori ini.
                                </div>
                            @else
                                <div class="table-responsive rounded-3 border">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>No. Kontrak</th>
                                                <th>Nama Karyawan</th>
                                                <th>Jabatan & Dept</th>
                                                <th>Cabang</th>
                                                <th>Akhir Kontrak</th>
                                                <th class="text-center">Sisa Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tab['items'] as $d)
                                                @php
                                                    $sisahari = hitungSisahari($d->sampai);
                                                    $isLate = $sisahari < 0;
                                                @endphp
                                                <tr class="{{ $isLate ? 'table-danger' : '' }}">
                                                    <td class="fw-semibold text-dark">{{ $d->no_kontrak }}</td>
                                                    <td>
                                                        <div class="fw-bold text-dark">{{ formatName($d->nama_karyawan) }}</div>
                                                        <small class="text-muted">NIK: {{ $d->nik }}</small>
                                                    </td>
                                                    <td>
                                                        <div>{{ singkatString($d->nama_jabatan) }}</div>
                                                        <small class="text-muted">{{ $d->kode_dept }}</small>
                                                    </td>
                                                    <td><span class="badge bg-label-secondary">{{ textupperCase($d->kode_cabang) }}</span></td>
                                                    <td class="fw-semibold">{{ formatIndo($d->sampai) }}</td>
                                                    <td class="text-center">
                                                        @if ($isLate)
                                                            <span class="badge bg-danger rounded-pill">Lewat {{ abs($sisahari) }} Hari</span>
                                                        @else
                                                            <span class="badge bg-label-warning rounded-pill">{{ $sisahari }} Hari Lagi</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 3. Birthday Widget -->
        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-warning bg-opacity-15 text-warning rounded-circle">
                        <i class="ti ti-cake fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Ulang Tahun Hari Ini</h5>
                        <small class="text-muted">Karyawan yang merayakan hari kelahiran hari ini</small>
                    </div>
                </div>
                <span class="badge bg-label-warning rounded-pill px-3 py-2 fs-7 fw-bold">
                    {{ count($birthday) }} Karyawan
                </span>
            </div>
            <div class="card-body">
                @if (count($birthday) > 0)
                    <div class="d-flex flex-wrap align-items-center justify-content-between p-3 rounded-3 mb-4" style="background: #fffbeb; border: 1px solid #fef3c7;">
                        <div class="d-flex align-items-center gap-2 mb-2 mb-sm-0">
                            <i class="ti ti-sparkles text-warning fs-4"></i>
                            <span class="fw-semibold text-warning-dark fs-7">Kirimkan ucapan selamat kepada rekan kerja yang berulang tahun!</span>
                        </div>
                        <button type="button" class="btn btn-success btn-sm d-inline-flex align-items-center gap-2 rounded-pill px-3" id="btnKirimUcapan" onclick="kirimUcapanSemua()">
                            <i class="ti ti-brand-whatsapp fs-5"></i>
                            <span id="btnText">Kirim Ucapan WA</span>
                            <span id="btnLoading" class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>

                    <div class="row g-3">
                        @foreach ($birthday as $d)
                            @php
                                $umur = \Carbon\Carbon::parse($d->tanggal_lahir)->age;
                            @endphp
                            <div class="col-12 col-md-6">
                                <div class="p-3 rounded-4 border border-slate-200 bg-white d-flex align-items-center gap-3 shadow-xs hover-elevate">
                                    <div class="position-relative">
                                        @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                            <img src="{{ getfotoKaryawan($d->foto) }}" alt="{{ $d->nama_karyawan }}" class="rounded-circle object-fit-cover shadow-sm border border-2 border-warning" style="width: 54px; height: 54px;">
                                        @else
                                            <div class="rounded-circle bg-label-warning d-flex align-items-center justify-content-center border border-2 border-warning fw-bold" style="width: 54px; height: 54px;">
                                                {{ strtoupper(substr($d->nama_karyawan, 0, 2)) }}
                                            </div>
                                        @endif
                                        <span class="position-absolute bottom-0 end-0 badge bg-warning rounded-circle p-1 border border-white" style="font-size: 0.65rem;">
                                            🎂
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="fw-bold mb-0 text-truncate">{{ $d->nama_karyawan }}</h6>
                                            <span class="badge bg-label-warning rounded-pill">{{ $umur }} Thn</span>
                                        </div>
                                        <div class="text-muted fs-7 text-truncate mt-1">
                                            <i class="ti ti-briefcase fs-7"></i> {{ $d->nama_jabatan }} &bull; {{ $d->kode_dept }}
                                        </div>
                                        <div class="text-muted fs-8 text-truncate">
                                            <i class="ti ti-map-pin fs-8"></i> {{ $d->nama_cabang }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="avatar avatar-lg bg-label-secondary mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="ti ti-cake-off fs-2 text-muted"></i>
                        </div>
                        <h6 class="fw-bold text-muted mb-1">Tidak ada karyawan ulang tahun hari ini</h6>
                        <p class="text-muted fs-7 mb-0">Semua perayaan hari kelahiran akan muncul otomatis di sini.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Right Column (4 cols): Department Breakdown & Demographics Charts -->
    <div class="col-12 col-lg-4">

        <!-- Department Attendance Breakdown -->
        @if(isset($rekap_dept) && count($rekap_dept) > 0)
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0">Kehadiran per Departemen</h6>
                    <i class="ti ti-chart-bar text-muted"></i>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        @foreach($rekap_dept as $dept)
                            @php
                                $deptRate = $dept->total_karyawan > 0 ? round(($dept->total_hadir / $dept->total_karyawan) * 100) : 0;
                            @endphp
                            <div>
                                <div class="d-flex align-items-center justify-content-between fs-7 mb-1">
                                    <span class="fw-semibold text-dark text-truncate" style="max-width: 170px;">{{ $dept->nama_dept }}</span>
                                    <span class="text-muted">
                                        <strong class="text-dark">{{ $dept->total_hadir }}</strong> / {{ $dept->total_karyawan }}
                                        <span class="badge bg-label-primary rounded-pill ms-1 fs-8">{{ $deptRate }}%</span>
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 999px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $deptRate }}%;" aria-valuenow="{{ $deptRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Chart 1: Status Karyawan -->
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0">Komposisi Status Karyawan</h6>
                    <small class="text-muted fs-8">Distribusi status kepegawaian aktif</small>
                </div>
                <i class="ti ti-chart-donut text-muted"></i>
            </div>
            <div class="card-body">
                {!! $chart->container() !!}
            </div>
        </div>

        <!-- Chart 2: Jenis Kelamin -->
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0">Komposisi Gender</h6>
                    <small class="text-muted fs-8">Perbandingan Laki-laki & Perempuan</small>
                </div>
                <i class="ti ti-gender-intergender text-muted"></i>
            </div>
            <div class="card-body">
                {!! $jkchart->container() !!}
            </div>
        </div>

        <!-- Chart 3: Tingkat Pendidikan -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0">Tingkat Pendidikan</h6>
                    <small class="text-muted fs-8">Distribusi jenjang pendidikan</small>
                </div>
                <i class="ti ti-school text-muted"></i>
            </div>
            <div class="card-body">
                {!! $pddchart->container() !!}
            </div>
        </div>

    </div>
</div>

@endsection

@push('myscript')
<script src="{{ $chart->cdn() }}"></script>
{{ $chart->script() }}
{{ $jkchart->script() }}
{{ $pddchart->script() }}

<script>
    // Live ticking clock for hero banner
    function updateLiveClock() {
        const clockElem = document.getElementById('realtimeClock');
        if (clockElem) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockElem.textContent = hours + ':' + minutes + ':' + seconds;
        }
    }
    setInterval(updateLiveClock, 1000);

    // Send Birthday Wishes via WhatsApp
    function kirimUcapanSemua() {
        const btnKirim = document.getElementById('btnKirimUcapan');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');

        btnKirim.disabled = true;
        btnText.textContent = 'Mengirim...';
        btnLoading.classList.remove('d-none');

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
            btnKirim.disabled = false;
            btnText.textContent = 'Kirim Ucapan WA';
            btnLoading.classList.add('d-none');

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            btnKirim.disabled = false;
            btnText.textContent = 'Kirim Ucapan WA';
            btnLoading.classList.add('d-none');

            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan: ' + error.message
            });
        });
    }
</script>
@endpush
