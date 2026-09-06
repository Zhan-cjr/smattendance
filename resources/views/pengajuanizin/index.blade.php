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
            background-color: #ffffff;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 18px -2px rgba(15, 23, 42, 0.05);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        /* Filter Tab Buttons */
        .tab-btn {
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .tab-btn.active {
            background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #0d9488 100%) !important;
            color: #ffffff !important;
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
            <div class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 text-center">
                <span class="text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider block">Menunggu</span>
                <span class="text-lg font-black text-amber-900 dark:text-amber-200 block mt-0.5">{{ $pendingCount }}</span>
            </div>

            <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 text-center">
                <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Disetujui</span>
                <span class="text-lg font-black text-emerald-900 dark:text-emerald-200 block mt-0.5">{{ $approvedCount }}</span>
            </div>

            <div class="p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-800/60 text-center">
                <span class="text-[10px] font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider block">Ditolak</span>
                <span class="text-lg font-black text-rose-900 dark:text-rose-200 block mt-0.5">{{ $rejectedCount }}</span>
            </div>
        </div>

        {{-- ===== CATEGORY FILTER TABS ===== --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 mb-3 scrollbar-none">
            <button type="button" onclick="filterIzin('all', this)" class="tab-btn active px-3 py-1.5 rounded-xl text-[11px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                Semua ({{ $totalCount }})
            </button>
            <button type="button" onclick="filterIzin('i', this)" class="tab-btn px-3 py-1.5 rounded-xl text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                📝 Absen
            </button>
            <button type="button" onclick="filterIzin('s', this)" class="tab-btn px-3 py-1.5 rounded-xl text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                🤒 Sakit
            </button>
            <button type="button" onclick="filterIzin('c', this)" class="tab-btn px-3 py-1.5 rounded-xl text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                🏖️ Cuti
            </button>
            <button type="button" onclick="filterIzin('d', this)" class="tab-btn px-3 py-1.5 rounded-xl text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
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
                                <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 truncate">
                                    {{ $ket_text }}
                                </h3>
                                <span class="text-[10.5px] font-semibold text-slate-400 block">
                                    {{ $totalDays }} Hari Pengajuan
                                </span>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full border shrink-0 {{ $statusBadge }}">
                            <i class="fa-solid {{ $statusIcon }} text-[9px]"></i>
                            <span>{{ $status_text }}</span>
                        </span>
                    </div>

                    {{-- Dates Box --}}
                    <div class="grid grid-cols-2 gap-2 mt-2.5">
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-2 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">Mulai</span>
                            <span class="text-xs font-black text-slate-700 dark:text-slate-200 mt-0.5 block">
                                {{ DateToIndo($d->dari) }}
                            </span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-2 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">Sampai</span>
                            <span class="text-xs font-black text-slate-700 dark:text-slate-200 mt-0.5 block">
                                {{ DateToIndo($d->sampai) }}
                            </span>
                        </div>
                    </div>

                    {{-- Reason / Keterangan --}}
                    <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-start justify-between gap-2">
                        <div class="text-[11.5px] text-slate-600 dark:text-slate-300 leading-snug flex-1">
                            <span class="font-bold text-slate-400 block text-[9.5px] uppercase">Keterangan:</span>
                            <span class="mt-0.5 block">{{ $d->keterangan ?? '-' }}</span>
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
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Belum Ada Pengajuan</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">
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