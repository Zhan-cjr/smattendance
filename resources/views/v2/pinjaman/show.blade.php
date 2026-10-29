@extends('v2.layouts.app')

@section('titlepage', 'Detail Pinjaman - ' . $pinjaman->no_pinjaman)
@section('page_title', 'Detail Pinjaman')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $k = $pinjaman->karyawan;
    $foto = $k->foto ?? null;
    $fotoUrl = (!empty($foto) && Storage::disk('public')->exists('/karyawan/' . $foto))
        ? getfotoKaryawan($foto)
        : asset('assets/img/avatars/No_Image_Available.jpg');

    $repaymentPercentage = $pinjaman->jumlah_pinjaman > 0 
        ? round(($pinjaman->total_dibayar / $pinjaman->jumlah_pinjaman) * 100) 
        : 0;

    $tglMasuk = $k->tanggal_masuk ? \Carbon\Carbon::parse($k->tanggal_masuk) : null;
    $masaKerja = '-';
    if ($tglMasuk) {
        $diff = $tglMasuk->diff(\Carbon\Carbon::now());
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' Thn';
        if ($diff->m > 0) $parts[] = $diff->m . ' Bln';
        $masaKerja = implode(', ', $parts) ?: 'Baru';
    }
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle with Back Button -->
        <div class="flex items-center gap-3">
            <a href="{{ route('pinjaman.index') }}" 
               class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-2xs cursor-pointer shrink-0">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('pinjaman.index') }}" class="hover:text-emerald-600 transition-colors">Pinjaman</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Detail Pinjaman</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2 mt-0.5">
                    <span>No. Pinjaman:</span>
                    <span class="font-mono text-emerald-700 font-extrabold">{{ $pinjaman->no_pinjaman }}</span>
                    @if($pinjaman->status == 'L')
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Lunas
                        </span>
                    @elseif($pinjaman->status == 'B')
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            Batal
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                            Aktif
                        </span>
                    @endif
                </h1>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            @if($pinjaman->sisa_pinjaman > 0 && $pinjaman->status != 'B')
                @can('pinjaman.pembayaran')
                    <button type="button" id="btnPembayaranManualV2" 
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-cash text-sm"></i>
                        <span>Bayar Manual</span>
                    </button>
                @endcan
            @endif
        </div>
    </div>

    <!-- ================= 2. EXECUTIVE METRICS CARD ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-credit-card text-[190px]"></i>
        </div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
            
            <!-- Left: Profile Karyawan & Jabatan (5 Cols) -->
            <div class="md:col-span-5 flex items-center gap-4">
                <div class="relative shrink-0">
                    <img src="{{ $fotoUrl }}" alt="Foto" 
                         class="w-16 h-16 rounded-2xl object-cover border-2 border-white/30 shadow-md">
                </div>
                <div class="min-w-0">
                    <h2 class="text-base sm:text-lg font-black text-white truncate">{{ $k->nama_karyawan }}</h2>
                    <div class="flex items-center gap-2 flex-wrap mt-1">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-white/20 text-white border border-white/30">
                            {{ $pinjaman->nik }}
                        </span>
                        <span class="text-xs text-emerald-100 font-semibold truncate">
                            {{ $k->jabatan->nama_jabatan ?? '-' }}
                        </span>
                    </div>
                    <div class="text-[11px] text-white/80 mt-1 flex items-center gap-2">
                        <span>{{ $k->cabang->nama_cabang ?? '-' }}</span>
                        <span>&bull;</span>
                        <span>{{ $k->departemen->nama_dept ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Middle: Financial Summary (4 Cols) -->
            <div class="md:col-span-4 border-t md:border-t-0 md:border-l border-white/20 pt-4 md:pt-0 md:pl-6">
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-100 block">Total Pokok</span>
                        <span class="text-xs sm:text-sm font-black text-white block mt-0.5">
                            Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-100 block">Dibayar</span>
                        <span class="text-xs sm:text-sm font-black text-emerald-200 block mt-0.5">
                            Rp {{ number_format($pinjaman->total_dibayar, 0, ',', '.') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-200 block">Sisa</span>
                        <span class="text-xs sm:text-sm font-black text-white block mt-0.5">
                            Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: Progress Pelunasan (3 Cols) -->
            <div class="md:col-span-3 border-t md:border-t-0 md:border-l border-white/20 pt-4 md:pt-0 md:pl-6 text-center md:text-right">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-100 block">Progress Pelunasan</span>
                <div class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-0.5">
                    {{ $repaymentPercentage }}%
                </div>
                <div class="w-full bg-white/20 rounded-full h-2 mt-2 overflow-hidden">
                    <div class="bg-white h-2 rounded-full transition-all duration-500" style="width: {{ $repaymentPercentage }}%"></div>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= 3. DUAL COLUMN CONTENT (RENCANA CICILAN & RIWAYAT PEMBAYARAN) ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- KOLOM KIRI: RENCANA CICILAN PINJAMAN (7 COLS - LEBIH LEBAR) -->
        <div class="lg:col-span-7 space-y-3">
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                
                <!-- Seamless Solid Card Header -->
                <div class="px-5 py-3.5 flex items-center justify-between gap-2 text-white border-0"
                     style="background-color: {{ $themeColor1 }};">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                            <i class="ti ti-list-numbers"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-white tracking-tight">Jadwal Rencana Cicilan</h3>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                        {{ $pinjaman->rencana_cicilan->count() }} Angsuran
                    </span>
                </div>

                <!-- Responsive Table Container -->
                <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
                    <table class="w-full text-left text-xs border-0 border-collapse whitespace-nowrap">
                        <!-- Matching Solid Table Header -->
                        <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                               style="background-color: {{ $themeColor1 }};">
                            <tr class="border-0 border-t-0 text-white/90">
                                <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">#</th>
                                <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Periode Siklus</th>
                                <th class="py-2.5 px-4 text-right text-white/90 border-0 border-t-0">Nominal</th>
                                <th class="py-2.5 px-4 text-right text-white/90 border-0 border-t-0">Realisasi</th>
                                <th class="py-2.5 px-4 text-right text-white/90 border-0 border-t-0">Sisa</th>
                                <th class="py-2.5 px-4 text-center text-white/90 border-0 border-t-0">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @php 
                                $totalRencana = 0; 
                                $totalRealisasi = 0;
                                $totalSisa = 0;
                            @endphp
                            @foreach ($pinjaman->rencana_cicilan as $rc)
                                @php 
                                    $totalRencana += $rc->jumlah_cicilan; 
                                    // Hitung realisasi pembayaran berdasarkan bulan dan tahun gaji
                                    $realisasi = $pinjaman->pembayaran_pinjaman
                                        ->where('bulan_gaji', $rc->bulan)
                                        ->where('tahun_gaji', $rc->tahun)
                                        ->sum('jumlah_bayar');
                                    $totalRealisasi += $realisasi;
                                    $sisaCicilan = max(0, $rc->jumlah_cicilan - $realisasi);
                                    $totalSisa += $sisaCicilan;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors {{ $realisasi >= $rc->jumlah_cicilan ? 'bg-emerald-50/20' : '' }}">
                                    <td class="py-2.5 px-3.5 text-center font-bold text-slate-500">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto text-[11px] font-black text-slate-700">
                                            {{ $rc->cicilan_ke }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 font-bold text-slate-800">
                                        <div class="flex items-center gap-1.5">
                                            <i class="ti ti-calendar text-xs text-slate-400"></i>
                                            <span>{{ getNamabulan($rc->bulan) }} {{ $rc->tahun }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-extrabold text-slate-900 font-mono">
                                        Rp {{ number_format($rc->jumlah_cicilan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-extrabold font-mono {{ $realisasi > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                        @if($realisasi > 0)
                                            Rp {{ number_format($realisasi, 0, ',', '.') }}
                                        @else
                                            <span class="text-slate-400 font-normal">-</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-extrabold font-mono {{ $sisaCicilan > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                        @if($sisaCicilan > 0)
                                            Rp {{ number_format($sisaCicilan, 0, ',', '.') }}
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-bold">
                                                <i class="ti ti-check text-xs"></i> 0
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-center">
                                        @if ($realisasi >= $rc->jumlah_cicilan || $rc->status == 'S')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="ti ti-check text-xs"></i> Lunas
                                            </span>
                                        @elseif($realisasi > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                                <i class="ti ti-clock text-xs"></i> Sebagian
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-slate-50 text-slate-600 border border-slate-200">
                                                <i class="ti ti-minus text-xs"></i> Belum Bayar
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-xs font-black">
                            <tr>
                                <td colspan="2" class="py-3 px-4 text-slate-700 uppercase tracking-wider text-[11px]">
                                    Total Rencana, Realisasi & Sisa
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-sm font-black text-slate-900">
                                    Rp {{ number_format($totalRencana, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-sm font-black text-emerald-700">
                                    Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-sm font-black {{ $totalSisa > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                    Rp {{ number_format($totalSisa, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center text-slate-500 text-[10.5px] font-bold">
                                    {{ $pinjaman->rencana_cicilan->where('status', 'S')->count() }} / {{ $pinjaman->rencana_cicilan->count() }} Lunas
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: RIWAYAT PEMBAYARAN PINJAMAN (5 COLS) -->
        <div class="lg:col-span-5 space-y-3">
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                
                <!-- Seamless Solid Card Header -->
                <div class="px-5 py-3.5 flex items-center justify-between gap-2 text-white border-0"
                     style="background-color: {{ $themeColor1 }};">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                            <i class="ti ti-receipt-2"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-white tracking-tight">Riwayat Pembayaran & Transaksi</h3>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                        {{ $pinjaman->pembayaran_pinjaman->count() }} Transaksi
                    </span>
                </div>

                <!-- Responsive Table Container -->
                <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
                    <table class="w-full text-left text-xs border-0 border-collapse whitespace-nowrap">
                        <!-- Matching Solid Table Header -->
                        <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                               style="background-color: {{ $themeColor1 }};">
                            <tr class="border-0 border-t-0 text-white/90">
                                <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">#</th>
                                <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">No. Bukti / Tgl</th>
                                <th class="py-2.5 px-3.5 text-white/90 border-0 border-t-0">Metode</th>
                                <th class="py-2.5 px-4 text-right text-white/90 border-0 border-t-0">Jumlah Bayar</th>
                                <th class="py-2.5 px-3.5 text-center w-16 text-white/90 border-0 border-t-0">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @php $totalBayar = 0; @endphp
                            @forelse ($pinjaman->pembayaran_pinjaman as $index => $pay)
                                @php
                                    $totalBayar += $pay->jumlah_bayar;
                                    $jenisText = 'Potong Gaji';
                                    $jenisBadge = 'bg-sky-50 text-sky-700 border-sky-200';
                                    if ($pay->jenis_pembayaran == 'M') {
                                        $jenisText = 'Manual Kasir';
                                        $jenisBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                    } elseif ($pay->jenis_pembayaran == 'P') {
                                        $jenisText = 'Pelunasan Full';
                                        $jenisBadge = 'bg-purple-50 text-purple-700 border-purple-200';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-2.5 px-3.5 text-center font-bold text-slate-500">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto text-[11px] font-black text-slate-700">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4">
                                        <div class="font-mono text-xs font-bold text-slate-800">{{ $pay->no_bukti }}</div>
                                        <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                            <i class="ti ti-calendar text-xs text-slate-400"></i>
                                            <span>{{ date('d/m/Y', strtotime($pay->tanggal_bayar)) }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $jenisBadge }}">
                                            {{ $jenisText }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-extrabold text-emerald-700 font-mono">
                                        Rp {{ number_format($pay->jumlah_bayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-3.5 text-center">
                                        @if ($pay->jenis_pembayaran != 'C')
                                            @can('pinjaman.pembayaran')
                                                <form action="{{ route('pinjaman.deletepembayaran', Crypt::encrypt($pay->id)) }}" method="POST" class="deleteform m-0 inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btnDeletePembayaran p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition active:scale-95 cursor-pointer"
                                                            title="Hapus Pembayaran">
                                                        <i class="ti ti-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        @else
                                            <span class="text-slate-300 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 bg-white">
                                        <i class="ti ti-receipt-off text-2xl text-slate-300 mb-1 d-block"></i>
                                        <span class="text-xs font-semibold text-slate-500">Belum ada riwayat transaksi pembayaran</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($pinjaman->pembayaran_pinjaman->count() > 0)
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-xs font-black">
                            <tr>
                                <td colspan="3" class="py-3 px-4 text-slate-700 uppercase tracking-wider text-[11px]">
                                    Total Pembayaran Masuk
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-sm font-black text-emerald-700">
                                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- ================= MODAL PEMBAYARAN MANUAL STANDAR V2 ================= -->
<div id="modalPembayaranManualV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Modal Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative shrink-0"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-cash text-lg"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight">
                        Pembayaran Pinjaman Manual
                    </h3>
                    <p class="text-[11px] text-slate-500">
                        Setoran tunai / pelunasan di luar payroll bulanan
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseModalPembayaran w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="loadModalPembayaranBody">
            <div class="py-12 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                <p class="text-xs font-semibold">Memuat formulir...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        // Open Modal Pembayaran
        $('#btnPembayaranManualV2').click(function(e) {
            e.preventDefault();
            $('#modalPembayaranManualV2').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            $('#loadModalPembayaranBody').html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat formulir...</p>
                </div>
            `);
            $('#loadModalPembayaranBody').load("{{ route('pinjaman.pembayaran', Crypt::encrypt($pinjaman->id)) }}", function() {
                if (typeof $.fn.maskMoney !== 'undefined') {
                    $('#loadModalPembayaranBody .money-pembayaran, #loadModalPembayaranBody #v2_jumlah_bayar').maskMoney({
                        thousands: '.', 
                        decimal: ',', 
                        precision: 0, 
                        allowZero: true
                    });
                }
                if (typeof flatpickr !== 'undefined') {
                    $('#loadModalPembayaranBody .flatpickr-pembayaran').flatpickr({
                        dateFormat: "Y-m-d",
                        allowInput: true
                    });
                }
            });
        });

        $(document).on('click', '.btnCloseModalPembayaran', function() {
            $('#modalPembayaranManualV2').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        });

        $('#modalPembayaranManualV2').click(function(e) {
            if (e.target === this) {
                $('#modalPembayaranManualV2').addClass('hidden');
                $('body').removeClass('overflow-hidden');
            }
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$('#modalPembayaranManualV2').hasClass('hidden')) {
                $('#modalPembayaranManualV2').addClass('hidden');
                $('body').removeClass('overflow-hidden');
            }
        });

        // Delete confirmation
        $(document).on('click', '.btnDeletePembayaran', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Pembayaran?',
                text: 'Nominal pembayaran ini akan dikembalikan ke sisa saldo pinjaman!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '{{ $themeColor1 }}',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-slate-100',
                    confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold shadow-xs',
                    cancelButton: 'px-4 py-2 rounded-xl text-xs font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
