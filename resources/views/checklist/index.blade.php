@extends('layouts.mobile.modern')
@section('title', 'Checklist Buka & Tutup Toko')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
<style>
    .checklist-hero {
        background: linear-gradient(145deg, #0f766e 0%, #115e59 50%, #042f2e 100%);
        border-radius: 24px;
        box-shadow: 0 10px 25px -5px rgba(15, 118, 110, 0.3);
    }
    .dark .checklist-hero {
        background: linear-gradient(145deg, #022c22 0%, #064e3b 50%, #031a15 100%);
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    /* Card Styling with Strict Light/Dark Contrast */
    .checklist-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .checklist-card:active {
        transform: scale(0.985);
    }
    .dark .checklist-card {
        background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
    }

    .card-title-text {
        color: #0f172a !important;
        font-weight: 800;
        font-size: 15px;
    }
    .dark .card-title-text {
        color: #f8fafc !important;
    }

    .card-desc-text {
        color: #475569 !important;
        font-size: 12px;
    }
    .dark .card-desc-text {
        color: #94a3b8 !important;
    }

    .notice-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 18px;
    }
    .dark .notice-box {
        background: rgba(245, 158, 11, 0.12) !important;
        border: 1px solid rgba(245, 158, 11, 0.35) !important;
    }
    .notice-title {
        color: #92400e !important;
        font-weight: 700;
        font-size: 12px;
    }
    .dark .notice-title {
        color: #fde68a !important;
    }
    .notice-desc {
        color: #78350f !important;
        font-size: 11px;
    }
    .dark .notice-desc {
        color: #fef3c7 !important;
    }

    .history-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        transition: all 0.15s ease;
    }
    .dark .history-card {
        background: #182339 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    .history-card:active {
        transform: scale(0.985);
    }
</style>
@endpush

@section('content')
<div class="px-2 pt-1 pb-24 max-w-md mx-auto">
    <!-- Outlet Info Hero Header -->
    <div class="checklist-hero p-4 text-white mb-4 relative overflow-hidden">
        <div class="absolute -right-3 -bottom-5 text-white/5 text-8xl pointer-events-none">
            <i class="fa-solid fa-store"></i>
        </div>
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-white/15 text-emerald-200 border border-white/20 flex items-center gap-1.5 backdrop-blur-md">
                <i class="fa-solid fa-location-dot text-[10px] text-emerald-300"></i>
                <span>{{ $karyawan->nama_cabang ?? 'Kantor / Toko' }}</span>
            </span>
            <span class="text-[11px] text-white/90 font-medium flex items-center gap-1">
                <i class="fa-regular fa-calendar text-[10px]"></i>
                <span>{{ \Carbon\Carbon::parse($today)->translatedFormat('d M Y') }}</span>
            </span>
        </div>
        <h2 class="text-xl font-black text-white tracking-tight">Inspeksi Buka & Tutup</h2>
        <p class="text-xs text-white/80 mt-1 leading-relaxed">
            Verifikasi fisik keamanan, pencegahan penyusup di area persembunyian, aset kasir, dan kesiapan operasional.
        </p>
    </div>

    <!-- Alert / Notice Keamanan Anti-Maling -->
    <div class="notice-box p-3.5 mb-4 flex items-start gap-3 shadow-2xs">
        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div>
            <h5 class="notice-title">Prosedur Keamanan Khusus</h5>
            <p class="notice-desc mt-0.5 leading-snug">
                Sebelum menutup toko, wajib memeriksa langsung toilet, kolong meja kasir, dan lorong gudang untuk memastikan tidak ada orang yang bersembunyi di dalam toko.
            </p>
        </div>
    </div>

    <!-- Action Cards: Opening & Closing -->
    <div class="space-y-3.5 mb-6">
        <!-- 1. OPENING CHECKLIST -->
        <div class="checklist-card p-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-2xs" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block" style="color: #d97706;">
                            SOP Pagi &bull; Buka Toko
                        </span>
                        <h4 class="card-title-text leading-tight mt-0.5">Opening Toko</h4>
                        <span class="card-desc-text block mt-0.5">Pemeriksaan fisik saat buka toko</span>
                    </div>
                </div>

                @if($openingToday)
                    <span class="px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1 shadow-2xs">
                        <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                        <span>Selesai</span>
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                        Belum Diisi
                    </span>
                @endif
            </div>

            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                @if($openingToday)
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                        Oleh: <strong class="text-slate-800 dark:text-slate-200">{{ $openingToday->karyawan->nama_karyawan ?? $openingToday->nik }}</strong> ({{ date('H:i', strtotime($openingToday->jam)) }} WIB)
                    </div>
                    <a href="{{ route('checklist.show', $openingToday->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 active:scale-95 transition-all text-decoration-none">
                        <span>Lihat Laporan</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                @else
                    <div class="text-[11px] text-slate-400 dark:text-slate-500">
                        Belum ada laporan hari ini
                    </div>
                    <a href="{{ route('checklist.create', 'opening') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white shadow-md active:scale-95 transition-all text-decoration-none" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
                        <i class="fa-solid fa-camera"></i>
                        <span>Mulai Opening</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- 2. CLOSING CHECKLIST -->
        <div class="checklist-card p-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-2xs" style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe;">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block" style="color: #4f46e5;">
                            SOP Malam &bull; Tutup Toko
                        </span>
                        <h4 class="card-title-text leading-tight mt-0.5">Closing Toko</h4>
                        <span class="card-desc-text block mt-0.5">Pemeriksaan anti-penyusup & gembok</span>
                    </div>
                </div>

                @if($closingToday)
                    <span class="px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1 shadow-2xs">
                        <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                        <span>Selesai</span>
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                        Belum Diisi
                    </span>
                @endif
            </div>

            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                @if($closingToday)
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                        Oleh: <strong class="text-slate-800 dark:text-slate-200">{{ $closingToday->karyawan->nama_karyawan ?? $closingToday->nik }}</strong> ({{ date('H:i', strtotime($closingToday->jam)) }} WIB)
                    </div>
                    <a href="{{ route('checklist.show', $closingToday->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 active:scale-95 transition-all text-decoration-none">
                        <span>Lihat Laporan</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                @else
                    <div class="text-[11px] text-slate-400 dark:text-slate-500">
                        Belum ada laporan hari ini
                    </div>
                    <a href="{{ route('checklist.create', 'closing') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white shadow-md active:scale-95 transition-all text-decoration-none" style="background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);">
                        <i class="fa-solid fa-camera"></i>
                        <span>Mulai Closing</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Riwayat Laporan Terakhir -->
    <div class="mt-5">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                Riwayat Laporan Terakhir
            </h4>
            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ $karyawan->nama_cabang }}</span>
        </div>

        @if($recentReports->isEmpty())
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-center">
                <i class="fa-solid fa-clipboard-list text-3xl text-slate-300 dark:text-slate-600 mb-2"></i>
                <p class="text-xs text-slate-500 dark:text-slate-400">Belum ada riwayat laporan tersimpan.</p>
            </div>
        @else
            <div class="space-y-2.5">
                @foreach($recentReports as $rep)
                    <a href="{{ route('checklist.show', $rep->id) }}" class="history-card block p-3 shadow-2xs hover:border-slate-300 dark:hover:border-slate-700 text-decoration-none">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm {{ $rep->tipe == 'opening' ? 'text-amber-700' : 'text-indigo-700' }}" style="background: {{ $rep->tipe == 'opening' ? '#fef3c7' : '#e0e7ff' }};">
                                    <i class="fa-solid {{ $rep->tipe == 'opening' ? 'fa-door-open' : 'fa-lock' }}"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-slate-900 dark:text-slate-100">
                                        {{ ucfirst($rep->tipe) }} Toko
                                    </h5>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($rep->tanggal)->translatedFormat('d M Y') }} &bull; {{ date('H:i', strtotime($rep->jam)) }} WIB
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10.5px] font-bold text-teal-600 dark:text-teal-400 block">
                                    {{ $rep->total_checkpoint }} Titik OK
                                </span>
                                <span class="text-[10px] text-slate-400 truncate max-w-[100px] block">
                                    {{ $rep->karyawan->nama_karyawan ?? $rep->nik }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $recentReports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
