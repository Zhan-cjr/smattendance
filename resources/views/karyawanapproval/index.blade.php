@extends('layouts.mobile.app')
@section('content')
    <style>
        #header-section {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        #content-section {
            margin-top: 70px;
            padding-top: 5px;
            position: relative;
            z-index: 1;
            padding-bottom: 80px;
        }

        .approval-summary {
            background: linear-gradient(135deg, #32745e 0%, #2a6350 50%, #1f4d3d 100%);
            border-radius: 14px;
            padding: 18px;
            color: white;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(50, 116, 94, 0.3);
        }

        .approval-summary .admin-name {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .approval-summary .admin-label {
            font-size: 11px;
            opacity: 0.8;
        }

        .approval-summary .count-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 10px 16px;
            text-align: center;
            min-width: 70px;
        }

        .approval-summary .count-number {
            font-size: 28px;
            font-weight: 800;
            line-height: 1;
        }

        .approval-summary .count-label {
            font-size: 10px;
            opacity: 0.9;
            margin-top: 2px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 0 8px 0;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title ion-icon {
            font-size: 18px;
        }

        .section-title .section-count {
            background: rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
        }

        .izin-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 10px;
            border: 1px solid #e8e8e8;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .izin-card:active {
            transform: scale(0.98);
        }

        .izin-card .card-inner {
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .izin-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .izin-icon ion-icon {
            font-size: 22px;
        }

        .izin-info {
            flex: 1;
            min-width: 0;
        }

        .izin-info .nama {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .izin-info .detail {
            font-size: 11px;
            color: #888;
            margin-top: 2px;
        }

        .izin-info .keterangan {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .izin-action {
            flex-shrink: 0;
            text-align: center;
        }

        .izin-action .step-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 8px;
            margin-bottom: 6px;
            display: inline-block;
        }

        .btn-proses {
            background: #32745e;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-proses:hover, .btn-proses:active {
            background: #2a6350;
            color: white;
            text-decoration: none;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            opacity: 0.6;
        }

        .empty-state ion-icon {
            font-size: 72px;
            color: #32745e;
        }

        .empty-state p {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }

        /* Type colors */
        .type-absen { color: #1e90ff; }
        .type-absen-bg { background: rgba(30, 144, 255, 0.1); }
        .type-sakit { color: #ff6384; }
        .type-sakit-bg { background: rgba(255, 99, 132, 0.1); }
        .type-cuti { color: #ff9f40; }
        .type-cuti-bg { background: rgba(255, 159, 64, 0.1); }
        .type-dinas { color: #32745e; }
        .type-dinas-bg { background: rgba(50, 116, 94, 0.1); }
    </style>

    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('shortcut.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Approval Delegasi</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="content-section">
        <div class="row" style="margin-top: 10px">
            <div class="col" style="padding: 0 15px;">

                {{-- Summary Card --}}
                <div class="approval-summary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="admin-label">
                                <ion-icon name="shield-checkmark-outline" style="vertical-align: middle;"></ion-icon>
                                Atas nama
                            </div>
                            <div class="admin-name">{{ $admin->name }}</div>
                            <div class="admin-label">{{ $admin->getRoleNames()->first() }}</div>
                        </div>
                        <div class="count-badge">
                            <div class="count-number">{{ $totalPending }}</div>
                            <div class="count-label">Pending</div>
                        </div>
                    </div>
                </div>

                {{-- Empty State --}}
                @if($totalPending == 0)
                    <div class="empty-state">
                        <ion-icon name="checkmark-done-circle-outline"></ion-icon>
                        <p>Semua izin sudah diproses.<br>Tidak ada yang menunggu approval.</p>
                    </div>
                @endif

                {{-- Izin Absen --}}
                @if($pendingIzinAbsen->count() > 0)
                    <div class="section-title type-absen">
                        <ion-icon name="calendar-outline"></ion-icon>
                        Izin Absen
                        <span class="section-count type-absen">{{ $pendingIzinAbsen->count() }}</span>
                    </div>
                    @foreach($pendingIzinAbsen as $izin)
                        <div class="izin-card">
                            <div class="card-inner">
                                <div class="izin-icon type-absen-bg">
                                    <ion-icon name="calendar-outline" class="type-absen"></ion-icon>
                                </div>
                                <div class="izin-info">
                                    <div class="nama">{{ $izin->nama_karyawan }}</div>
                                    <div class="detail">
                                        {{ $izin->nama_dept ?? '-' }} • {{ date('d/m/Y', strtotime($izin->dari)) }} - {{ date('d/m/Y', strtotime($izin->sampai)) }}
                                    </div>
                                    <div class="keterangan">{{ $izin->keterangan }}</div>
                                </div>
                                <div class="izin-action">
                                    <div class="step-badge" style="background: rgba(30,144,255,0.1); color: #1e90ff;">Tahap {{ $izin->approval_step }}</div>
                                    <a href="{{ route('karyawan-approval.izinabsen.approve', Crypt::encrypt($izin->kode_izin)) }}" class="btn-proses">
                                        <ion-icon name="checkmark-outline"></ion-icon> Proses
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Izin Sakit --}}
                @if($pendingIzinSakit->count() > 0)
                    <div class="section-title type-sakit">
                        <ion-icon name="medkit-outline"></ion-icon>
                        Izin Sakit
                        <span class="section-count type-sakit">{{ $pendingIzinSakit->count() }}</span>
                    </div>
                    @foreach($pendingIzinSakit as $izin)
                        <div class="izin-card">
                            <div class="card-inner">
                                <div class="izin-icon type-sakit-bg">
                                    <ion-icon name="medkit-outline" class="type-sakit"></ion-icon>
                                </div>
                                <div class="izin-info">
                                    <div class="nama">{{ $izin->nama_karyawan }}</div>
                                    <div class="detail">
                                        {{ $izin->nama_dept ?? '-' }} • {{ date('d/m/Y', strtotime($izin->dari)) }} - {{ date('d/m/Y', strtotime($izin->sampai)) }}
                                    </div>
                                    <div class="keterangan">{{ $izin->keterangan }}</div>
                                </div>
                                <div class="izin-action">
                                    <div class="step-badge" style="background: rgba(255,99,132,0.1); color: #ff6384;">Tahap {{ $izin->approval_step }}</div>
                                    <a href="{{ route('karyawan-approval.izinsakit.approve', Crypt::encrypt($izin->kode_izin_sakit)) }}" class="btn-proses">
                                        <ion-icon name="checkmark-outline"></ion-icon> Proses
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Izin Cuti --}}
                @if($pendingIzinCuti->count() > 0)
                    <div class="section-title type-cuti">
                        <ion-icon name="airplane-outline"></ion-icon>
                        Izin Cuti
                        <span class="section-count type-cuti">{{ $pendingIzinCuti->count() }}</span>
                    </div>
                    @foreach($pendingIzinCuti as $izin)
                        <div class="izin-card">
                            <div class="card-inner">
                                <div class="izin-icon type-cuti-bg">
                                    <ion-icon name="airplane-outline" class="type-cuti"></ion-icon>
                                </div>
                                <div class="izin-info">
                                    <div class="nama">{{ $izin->nama_karyawan }}</div>
                                    <div class="detail">
                                        {{ $izin->nama_dept ?? '-' }} • {{ date('d/m/Y', strtotime($izin->dari)) }} - {{ date('d/m/Y', strtotime($izin->sampai)) }}
                                    </div>
                                    <div class="keterangan">{{ $izin->keterangan }}</div>
                                </div>
                                <div class="izin-action">
                                    <div class="step-badge" style="background: rgba(255,159,64,0.1); color: #ff9f40;">Tahap {{ $izin->approval_step }}</div>
                                    <a href="{{ route('karyawan-approval.izincuti.approve', Crypt::encrypt($izin->kode_izin_cuti)) }}" class="btn-proses">
                                        <ion-icon name="checkmark-outline"></ion-icon> Proses
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Izin Dinas --}}
                @if($pendingIzinDinas->count() > 0)
                    <div class="section-title type-dinas">
                        <ion-icon name="briefcase-outline"></ion-icon>
                        Izin Dinas
                        <span class="section-count type-dinas">{{ $pendingIzinDinas->count() }}</span>
                    </div>
                    @foreach($pendingIzinDinas as $izin)
                        <div class="izin-card">
                            <div class="card-inner">
                                <div class="izin-icon type-dinas-bg">
                                    <ion-icon name="briefcase-outline" class="type-dinas"></ion-icon>
                                </div>
                                <div class="izin-info">
                                    <div class="nama">{{ $izin->nama_karyawan }}</div>
                                    <div class="detail">
                                        {{ $izin->nama_dept ?? '-' }} • {{ date('d/m/Y', strtotime($izin->dari)) }} - {{ date('d/m/Y', strtotime($izin->sampai)) }}
                                    </div>
                                    <div class="keterangan">{{ $izin->keterangan }}</div>
                                </div>
                                <div class="izin-action">
                                    <div class="step-badge" style="background: rgba(50,116,94,0.1); color: #32745e;">Tahap {{ $izin->approval_step }}</div>
                                    <a href="{{ route('karyawan-approval.izindinas.approve', Crypt::encrypt($izin->kode_izin_dinas)) }}" class="btn-proses">
                                        <ion-icon name="checkmark-outline"></ion-icon> Proses
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </div>
@endsection
