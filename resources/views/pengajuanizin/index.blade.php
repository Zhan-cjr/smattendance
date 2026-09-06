@extends('layouts.mobile.modern')

@section('title')
    <div class="text-center leading-tight">
        <div class="font-extrabold text-[15px] tracking-tight">Pengajuan Izin</div>
        <div class="text-[9.5px] font-medium opacity-75">SM-Attendance</div>
    </div>
@endsection

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@section('header_right')
    <button type="button" onclick="showAjukanIzinModal()" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/20 text-white text-xs font-bold active:scale-90 transition-all shadow-sm">
        <i class="fa-solid fa-plus text-sm"></i>
    </button>
@endsection

@push('mystyle')
    <script>
        (function() {
            var savedTheme = localStorage.getItem('smatt_theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark', 'dark-mode-active');
                if (document.body) {
                    document.body.classList.remove('dark', 'dark-mode-active');
                }
            }
        })();
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        /* Bento Card Izin */
        .izin-card {
            background-color: #ffffff !important;
            border-radius: 20px !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 4px 18px -2px rgba(15, 23, 42, 0.05) !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        /* Typography & Content within Izin Card */
        .izin-title-text {
            color: #0f172a !important;
            font-size: 13.5px !important;
            font-weight: 800 !important;
            line-height: 1.25 !important;
        }

        .izin-subtitle-text {
            color: #64748b !important;
            font-size: 10.5px !important;
            font-weight: 600 !important;
        }

        .izin-date-box {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 6px 10px !important;
        }

        .izin-date-label {
            font-size: 9.5px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
            display: block !important;
        }

        .izin-date-val {
            font-size: 12px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            margin-top: 2px !important;
            display: block !important;
        }

        .izin-keterangan-text {
            font-size: 11.5px !important;
            color: #334155 !important;
            line-height: 1.4 !important;
        }

        .izin-keterangan-label {
            font-size: 9.5px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
            display: block !important;
        }

        /* Stats Cards */
        .stat-card {
            padding: 10px 8px !important;
            border-radius: 16px !important;
            text-align: center !important;
            border: 1px solid transparent !important;
        }

        .stat-card.stat-pending {
            background-color: #fef3c7 !important;
            border-color: #fde68a !important;
        }
        .stat-card.stat-pending .stat-label {
            color: #92400e !important;
        }
        .stat-card.stat-pending .stat-val {
            color: #78350f !important;
        }

        .stat-card.stat-approved {
            background-color: #d1fae5 !important;
            border-color: #a7f3d0 !important;
        }
        .stat-card.stat-approved .stat-label {
            color: #065f46 !important;
        }
        .stat-card.stat-approved .stat-val {
            color: #064e3b !important;
        }

        .stat-card.stat-rejected {
            background-color: #ffe4e6 !important;
            border-color: #fecdd3 !important;
        }
        .stat-card.stat-rejected .stat-label {
            color: #9f1239 !important;
        }
        .stat-card.stat-rejected .stat-val {
            color: #881337 !important;
        }

        .stat-label {
            font-size: 10px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            display: block !important;
        }

        .stat-val {
            font-size: 18px !important;
            font-weight: 900 !important;
            display: block !important;
            margin-top: 1px !important;
        }

        /* Filter Tab Buttons */
        .tab-btn {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            padding: 6px 12px !important;
            border-radius: 12px !important;
            transition: all 0.2s ease !important;
            white-space: nowrap !important;
        }
        .tab-btn.active {
            background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #0d9488 100%) !important;
            color: #ffffff !important;
            border-color: transparent !important;
            font-weight: 800 !important;
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.3) !important;
        }

        /* Modern Floating Action Button */
        .fab-modern {
            position: fixed;
            bottom: 78px;
            right: 18px;
            width: 52px;
            height: 52px;
            border-radius: 18px;
            background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #0d9488 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 8px 24px rgba(15, 118, 110, 0.45);
            z-index: 50;
            border: 2.5px solid #ffffff;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .fab-modern:active {
            transform: scale(0.92);
        }

        /* Dark Mode Overrides */
        html.dark body, body.dark {
            background-color: #070b14 !important;
            color: #f8fafc !important;
        }

        html.dark .izin-card, body.dark .izin-card {
            background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 8px 25px -4px rgba(0, 0, 0, 0.4) !important;
        }

        html.dark .izin-title-text, body.dark .izin-title-text {
            color: #f8fafc !important;
        }

        html.dark .izin-subtitle-text, body.dark .izin-subtitle-text {
            color: #94a3b8 !important;
        }

        html.dark .izin-date-box, body.dark .izin-date-box {
            background-color: #182339 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        html.dark .izin-date-label, body.dark .izin-date-label {
            color: #94a3b8 !important;
        }

        html.dark .izin-date-val, body.dark .izin-date-val {
            color: #f8fafc !important;
        }

        html.dark .izin-keterangan-text, body.dark .izin-keterangan-text {
            color: #cbd5e1 !important;
        }

        html.dark .izin-keterangan-label, body.dark .izin-keterangan-label {
            color: #94a3b8 !important;
        }

        html.dark .stat-card.stat-pending, body.dark .stat-card.stat-pending {
            background-color: rgba(120, 53, 15, 0.25) !important;
            border-color: rgba(245, 158, 11, 0.3) !important;
        }
        html.dark .stat-card.stat-pending .stat-label, body.dark .stat-card.stat-pending .stat-label {
            color: #fbbf24 !important;
        }
        html.dark .stat-card.stat-pending .stat-val, body.dark .stat-card.stat-pending .stat-val {
            color: #fef3c7 !important;
        }

        html.dark .stat-card.stat-approved, body.dark .stat-card.stat-approved {
            background-color: rgba(6, 78, 59, 0.25) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
        }
        html.dark .stat-card.stat-approved .stat-label, body.dark .stat-card.stat-approved .stat-label {
            color: #34d399 !important;
        }
        html.dark .stat-card.stat-approved .stat-val, body.dark .stat-card.stat-approved .stat-val {
            color: #d1fae5 !important;
        }

        html.dark .stat-card.stat-rejected, body.dark .stat-card.stat-rejected {
            background-color: rgba(136, 19, 55, 0.25) !important;
            border-color: rgba(244, 63, 94, 0.3) !important;
        }
        html.dark .stat-card.stat-rejected .stat-label, body.dark .stat-card.stat-rejected .stat-label {
            color: #fb7185 !important;
        }
        html.dark .stat-card.stat-rejected .stat-val, body.dark .stat-card.stat-rejected .stat-val {
            color: #ffe4e6 !important;
        }

        html.dark .tab-btn, body.dark .tab-btn {
            background-color: #1e293b !important;
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        html.dark .tab-btn.active, body.dark .tab-btn.active {
            background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #0d9488 100%) !important;
            color: #ffffff !important;
        }

        html.dark .fab-modern, body.dark .fab-modern {
            border-color: #0f172a !important;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5) !important;
        }
    </style>
@endpush

@section('content')
    <div class="px-1 pt-1 pb-28">

        @php
            $totalCount = count($pengajuan_izin);
            $pendingCount = $pengajuan_izin->where('status_izin', 0)->count();
            $approvedCount = $pengajuan_izin->where('status_izin', 1)->count();
            $rejectedCount = $pengajuan_izin->where('status_izin', 2)->count();
        @endphp

        {{-- ===== STATS OVERVIEW BENTO ===== --}}
        <div class="grid grid-cols-3 gap-2 mb-3.5">
            <div class="stat-card stat-pending">
                <span class="stat-label">Menunggu</span>
                <span class="stat-val">{{ $pendingCount }}</span>
            </div>

            <div class="stat-card stat-approved">
                <span class="stat-label">Disetujui</span>
                <span class="stat-val">{{ $approvedCount }}</span>
            </div>

            <div class="stat-card stat-rejected">
                <span class="stat-label">Ditolak</span>
                <span class="stat-val">{{ $rejectedCount }}</span>
            </div>
        </div>

        {{-- ===== CATEGORY FILTER TABS ===== --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 mb-3 scrollbar-none">
            <button type="button" onclick="filterIzin('all', this)" class="tab-btn active">
                Semua ({{ $totalCount }})
            </button>
            <button type="button" onclick="filterIzin('i', this)" class="tab-btn">
                📝 Absen
            </button>
            <button type="button" onclick="filterIzin('s', this)" class="tab-btn">
                🤒 Sakit
            </button>
            <button type="button" onclick="filterIzin('c', this)" class="tab-btn">
                🏖️ Cuti
            </button>
            <button type="button" onclick="filterIzin('d', this)" class="tab-btn">
                💼 Dinas
            </button>
        </div>

        {{-- ===== SKELETON LOADER ===== --}}
        <div id="skeleton-loader" class="space-y-3">
            @for ($i = 0; $i < 3; $i++)
                <div class="izin-card p-4 flex items-center gap-3">
                    <div class="skeleton-avatar sk shrink-0 w-12 h-12 rounded-2xl"></div>
                    <div class="flex-1 space-y-2">
                        <div class="skeleton-text w-32 sk"></div>
                        <div class="skeleton-text w-48 sk"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- ===== REAL CONTENT ===== --}}
        <div id="real-content" style="display:none;" class="space-y-3">
            @forelse ($pengajuan_izin as $index => $d)
                @php
                    if ($d->ket == 'i') {
                        $route = 'izinabsen.delete';
                        $ket_text = 'Izin Absen';
                        $iconClass = 'fa-file-lines text-sky-600 dark:text-sky-400';
                        $iconBox = 'bg-sky-50 dark:bg-sky-950/60 border-sky-200/80 dark:border-sky-800/60';
                    } elseif ($d->ket == 's') {
                        $route = 'izinsakit.delete';
                        $ket_text = 'Izin Sakit';
                        $iconClass = 'fa-notes-medical text-rose-600 dark:text-rose-400';
                        $iconBox = 'bg-rose-50 dark:bg-rose-950/60 border-rose-200/80 dark:border-rose-800/60';
                    } elseif ($d->ket == 'c') {
                        $route = 'izincuti.delete';
                        $ket_text = 'Izin Cuti';
                        $iconClass = 'fa-umbrella-beach text-amber-600 dark:text-amber-400';
                        $iconBox = 'bg-amber-50 dark:bg-amber-950/60 border-amber-200/80 dark:border-amber-800/60';
                    } else {
                        $route = 'izindinas.delete';
                        $ket_text = 'Izin Dinas';
                        $iconClass = 'fa-briefcase text-indigo-600 dark:text-indigo-400';
                        $iconBox = 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-200/80 dark:border-indigo-800/60';
                    }
                    
                    $tglDari = date('d', strtotime($d->dari));
                    $dayShort = strtoupper(substr(date('D', strtotime($d->dari)), 0, 3));
                    $status_text = $d->status_izin == 0 ? 'Pending' : ($d->status_izin == 1 ? 'Disetujui' : 'Ditolak');
                    
                    if ($d->status_izin == 0) {
                        $statusBadge = 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border-amber-300/80 dark:border-amber-700/80';
                        $statusIcon = 'fa-hourglass-half animate-pulse';
                    } elseif ($d->status_izin == 1) {
                        $statusBadge = 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border-emerald-300/80 dark:border-emerald-700/80';
                        $statusIcon = 'fa-check';
                    } else {
                        $statusBadge = 'bg-rose-100 dark:bg-rose-950/80 text-rose-800 dark:text-rose-300 border-rose-300/80 dark:border-rose-700/80';
                        $statusIcon = 'fa-xmark';
                    }

                    $totalDays = (int) round((strtotime($d->sampai) - strtotime($d->dari)) / (60 * 60 * 24)) + 1;
                @endphp
                
                <div class="izin-card p-4 fade-up izin-item" data-ket="{{ $d->ket }}" style="animation-delay: {{ $index * 0.03 }}s;">
                    {{-- Header Row: Type Icon, Title & Status Badge --}}
                    <div class="flex items-center justify-between gap-2 pb-2.5 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm border shrink-0 {{ $iconBox }}">
                                <i class="fa-solid {{ $iconClass }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="izin-title-text truncate">
                                    {{ $ket_text }}
                                </div>
                                <div class="izin-subtitle-text mt-0.5">
                                    {{ $totalDays }} Hari Pengajuan
                                </div>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full border shrink-0 {{ $statusBadge }}">
                            <i class="fa-solid {{ $statusIcon }} text-[9px]"></i>
                            <span>{{ $status_text }}</span>
                        </span>
                    </div>

                    {{-- Dates Box --}}
                    <div class="grid grid-cols-2 gap-2 mt-2.5">
                        <div class="izin-date-box">
                            <span class="izin-date-label">Mulai</span>
                            <span class="izin-date-val">
                                {{ DateToIndo($d->dari) }}
                            </span>
                        </div>
                        <div class="izin-date-box">
                            <span class="izin-date-label">Sampai</span>
                            <span class="izin-date-val">
                                {{ DateToIndo($d->sampai) }}
                            </span>
                        </div>
                    </div>

                    {{-- Reason / Keterangan --}}
                    <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-start justify-between gap-2">
                        <div class="leading-snug flex-1">
                            <span class="izin-keterangan-label">Keterangan:</span>
                            <span class="izin-keterangan-text mt-0.5 block">{{ $d->keterangan ?? '-' }}</span>
                        </div>

                        {{-- Action Button (Delete if Pending) --}}
                        @if ($d->status_izin == 0)
                            <form method="POST" action="{{ route($route, Crypt::encrypt($d->kode)) }}" class="deleteform shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-delete-izin px-2.5 py-1 rounded-xl text-[10.5px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 flex items-center gap-1 active:scale-90 transition-transform">
                                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    <span>Batalkan</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm">
                    <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                        <i class="fa-solid fa-calendar-plus"></i>
                    </div>
                    <div class="text-sm font-extrabold text-slate-800 dark:text-slate-100">Belum Ada Pengajuan</div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">
                        Anda belum memiliki riwayat pengajuan izin, sakit, cuti, atau dinas.
                    </p>
                    <button type="button" onclick="showAjukanIzinModal()" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white shadow-md active:scale-95 transition-all" style="background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #0d9488 100%);">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Buat Pengajuan Baru
                    </button>
                </div>
            @endforelse
        </div>

        {{-- ===== FLOATING ACTION BUTTON ===== --}}
        <div class="fab-modern" onclick="showAjukanIzinModal()" title="Ajukan Izin Baru">
            <i class="fa-solid fa-plus"></i>
        </div>

    </div>
@endsection

@push('myscript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                $('#skeleton-loader').fadeOut(150, function() {
                    $('#real-content').fadeIn(200);
                });
            }, 300);
        });

        // Filter tab function
        function filterIzin(type, btn) {
            $('.tab-btn').removeClass('active');
            $(btn).addClass('active');

            if (type === 'all') {
                $('.izin-item').fadeIn(150);
            } else {
                $('.izin-item').hide();
                $(`.izin-item[data-ket="${type}"]`).fadeIn(150);
            }
        }

        // Ajukan Izin Selection Modal
        function showAjukanIzinModal() {
            Swal.fire({
                title: '<div class="font-extrabold text-base text-slate-800 dark:text-white" style="font-family:\'Plus Jakarta Sans\',sans-serif;letter-spacing:-0.02em;">Pilih Jenis Pengajuan</div>',
                html: `
                    <div style="font-family:'Plus Jakarta Sans',sans-serif;padding:4px 0 8px;">
                        <p style="font-size:12px;color:#64748b;margin-bottom:14px;">Silakan pilih tipe permohonan yang ingin diajukan:</p>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <a href="{{ route('izinabsen.create') }}" style="
                                display:flex;align-items:center;gap:12px;padding:12px 14px;
                                background:linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
                                color:#ffffff;border-radius:14px;text-decoration:none;
                                box-shadow:0 4px 12px rgba(2,132,199,0.25);
                            ">
                                <span style="font-size:18px;">📝</span>
                                <div style="text-align:left;flex:1;">
                                    <div style="font-weight:700;font-size:13.5px;">Izin Absen</div>
                                    <div style="font-size:10.5px;opacity:0.85;">Izin keperluan pribadi / mendesak</div>
                                </div>
                                <span style="font-weight:bold;">➔</span>
                            </a>

                            <a href="{{ route('izinsakit.create') }}" style="
                                display:flex;align-items:center;gap:12px;padding:12px 14px;
                                background:linear-gradient(135deg, #e11d48 0%, #be123c 100%);
                                color:#ffffff;border-radius:14px;text-decoration:none;
                                box-shadow:0 4px 12px rgba(225,29,72,0.25);
                            ">
                                <span style="font-size:18px;">🤒</span>
                                <div style="text-align:left;flex:1;">
                                    <div style="font-weight:700;font-size:13.5px;">Izin Sakit</div>
                                    <div style="font-size:10.5px;opacity:0.85;">Izin karena sakit & surat dokter</div>
                                </div>
                                <span style="font-weight:bold;">➔</span>
                            </a>

                            <a href="{{ route('izincuti.create') }}" style="
                                display:flex;align-items:center;gap:12px;padding:12px 14px;
                                background:linear-gradient(135deg, #d97706 0%, #b45309 100%);
                                color:#ffffff;border-radius:14px;text-decoration:none;
                                box-shadow:0 4px 12px rgba(217,119,6,0.25);
                            ">
                                <span style="font-size:18px;">🏖️</span>
                                <div style="text-align:left;flex:1;">
                                    <div style="font-weight:700;font-size:13.5px;">Izin Cuti</div>
                                    <div style="font-size:10.5px;opacity:0.85;">Cuti tahunan / melahirkan / khusus</div>
                                </div>
                                <span style="font-weight:bold;">➔</span>
                            </a>

                            <a href="{{ route('izindinas.create') }}" style="
                                display:flex;align-items:center;gap:12px;padding:12px 14px;
                                background:linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
                                color:#ffffff;border-radius:14px;text-decoration:none;
                                box-shadow:0 4px 12px rgba(79,70,229,0.25);
                            ">
                                <span style="font-size:18px;">💼</span>
                                <div style="text-align:left;flex:1;">
                                    <div style="font-weight:700;font-size:13.5px;">Izin Dinas Luar</div>
                                    <div style="font-size:10.5px;opacity:0.85;">Tugas luar kota atau dinas kantor</div>
                                </div>
                                <span style="font-weight:bold;">➔</span>
                            </a>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Tutup',
                cancelButtonColor: '#94a3b8',
                customClass: {
                    popup: 'swal2-modern-choice-popup',
                    cancelButton: 'swal2-modern-cancel-btn'
                }
            });
        }

        // Delete / Cancel confirmation
        $(document).on('click', '.btn-delete-izin', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Batalkan Pengajuan?',
                text: 'Permohonan izin ini akan dihapus dari sistem.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Kembali',
                customClass: {
                    popup: 'swal2-modern-choice-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@endpush