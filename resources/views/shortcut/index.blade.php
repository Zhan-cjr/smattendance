@extends('layouts.mobile.modern')
@section('title', 'Semua Menu')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background-color: #f8fafc; /* matching ajuan jadwal */
        }
        
        .card.press {
            border: none;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 0;
            display: block; /* For anchor tags wrapper */
            text-decoration: none !important;
        }

        .card.press:active {
            transform: scale(0.98);
            box-shadow: 0 0 0 transparent;
            background: #f8fafc;
        }

        /* Smooth entrance animation */
        .fade-up {
            animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(15px);
        }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Notification Badge modern styling */
        .badge.modern-badge {
            background: #ef4444; /* red-500 */
            color: white;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 6px;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    <div class="px-1 pt-2 pb-24">
        
        <div class="space-y-1.5 pt-1" id="shortcut-list">
            
            {{-- Personal --}}
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-2 pt-2 pb-1 fade-up" style="animation-delay: 0.1s">Personal</div>
            
            <a href="{{ route('karyawan.idcard', Crypt::encrypt($karyawan->nik)) }}" class="card press fade-up" style="animation-delay: 0.15s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #e0f2fe; color: #0284c7;">
                            <ion-icon name="id-card-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">ID Card</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Lihat Kartu Identitas Digital</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>

            <a href="{{ route('slipgaji.index') }}" class="card press fade-up" style="animation-delay: 0.2s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #2a635020; color: #2a6350;">
                            <ion-icon name="cash-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Slip Gaji</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Download Bukti Gaji Bulanan</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>
            
            <a href="{{ route('kontrak.index') }}" class="card press fade-up" style="animation-delay: 0.25s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #fef3c7; color: #d97706;">
                            <ion-icon name="document-attach-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Dokumen Kontrak</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Lihat Masa Berlaku Kontrak</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>

            <a href="{{ route('pelanggaran.index') }}" class="card press fade-up" style="animation-delay: 0.3s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #fee2e2; color: #dc2626;">
                            <ion-icon name="warning-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Pelanggaran (SP)</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Riwayat Surat Peringatan</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(isset($total_pelanggaran) && $total_pelanggaran > 0)
                            <span class="badge modern-badge">{{ $total_pelanggaran }}</span>
                        @endif
                        <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                    </div>
                </div>
            </a>

            <a href="{{ route('shortcut.mypinjaman') }}" class="card press fade-up" style="animation-delay: 0.32s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #f0fdfa; color: #0d9488;">
                            <ion-icon name="wallet-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Pinjaman (PJP)</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Riwayat & Saldo Pinjaman</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>
            
            {{-- Presensi & Daily --}}
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-2 pt-3 pb-1 fade-up" style="animation-delay: 0.35s">Absensi & Harian</div>

            <a href="{{ route('myschedule.index') }}" class="card press fade-up" style="animation-delay: 0.38s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #ecfdf5; color: #10b981;">
                            <ion-icon name="calendar-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Jadwal Saya</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Lihat Jadwal Kerja Bulanan</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>

            <a href="{{ route('presensiistirahat.create') }}" class="card press fade-up" style="animation-delay: 0.4s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #ffedd5; color: #ea580c;">
                            <ion-icon name="cafe-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Absen Istirahat</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Catat Keluar Masuk Istirahat</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>

            <a href="{{ route('lembur.index') }}" class="card press fade-up" style="animation-delay: 0.45s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #f3e8ff; color: #9333ea;">
                            <ion-icon name="time-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Lembur Harian</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Riwayat Pekerjaan Lembur</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>

            <a href="{{ route('ajuanjadwal.index') }}" class="card press fade-up" style="animation-delay: 0.5s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #e0e7ff; color: #4f46e5;">
                            <ion-icon name="calendar-number-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Tukar Shift</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Ajukan Perubahan Jadwal Kerja</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>

            <a href="{{ route('kpi.transactions.myscore') }}" class="card press fade-up" style="animation-delay: 0.55s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #dcfce7; color: #16a34a;">
                            <ion-icon name="trending-up-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Penilaian KPI</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Key Performance Indicator</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>

            <a href="{{ route('facerecognition.karyawan.create') }}" class="card press fade-up" style="animation-delay: 0.6s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #f1f5f9; color: #475569;">
                            <ion-icon name="scan-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Daftar Face ID</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Rekam Data Wajah Presensi</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>

            {{-- Special Presensi (Hanya untuk NIK 11.11.111) --}}
            {{-- @if($karyawan->nik === '11.11.111')
            <a href="{{ route('presensispc.create') }}" class="card press fade-up" style="animation-delay: 0.62s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #fef08a; color: #a16207;">
                            <ion-icon name="star-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Presensi Khusus</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Input Absensi Manual</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>
            @endif --}}

            {{-- Dinamis --}}
            @if(auth()->user()->can('aktivitaskaryawan.index') || auth()->user()->can('kunjungan.index') || (isset($hasApprovalAccess) && $hasApprovalAccess))
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-2 pt-3 pb-1 fade-up" style="animation-delay: 0.65s">Khusus</div>
            @endif

            @can('aktivitaskaryawan.index')
            <a href="{{ route('aktivitaskaryawan.index') }}" class="card press fade-up" style="animation-delay: 0.7s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #ffe4e6; color: #e11d48;">
                            <ion-icon name="pulse-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Aktivitas Karyawan</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Laporan Kegiatan Karyawan</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>
            @endcan

            @can('kunjungan.index')
            <a href="{{ route('kunjungan.index') }}" class="card press fade-up" style="animation-delay: 0.75s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #fae8ff; color: #c026d3;">
                            <ion-icon name="map-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Kunjungan / Visit</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Titik Kunjungan Pelanggan</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a> 
            @endcan

            @if(isset($hasApprovalAccess) && $hasApprovalAccess)
            <a href="{{ route('karyawan-approval.index') }}" class="card press fade-up" style="animation-delay: 0.8s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #ccfbf1; color: #0f766e;">
                            <ion-icon name="checkmark-done-circle-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Hak Approval</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Persetujuan Pengajuan</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(isset($pendingApprovalCount) && $pendingApprovalCount > 0)
                            <span class="badge modern-badge">{{ $pendingApprovalCount }}</span>
                        @endif
                        <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                    </div>
                </div>
            </a>
            @endif

            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-2 pt-3 pb-1 fade-up" style="animation-delay: 0.85s">Umum</div>

            <a href="{{ route('pengumuman.index') }}" class="card press fade-up" style="animation-delay: 0.9s">
                <div class="card-body p-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #fdf4ff; color: #a21caf;">
                            <ion-icon name="megaphone-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Pengumuman</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Pusat Informasi Perusahaan</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>

        </div>
    </div>
@endsection
