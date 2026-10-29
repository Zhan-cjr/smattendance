@extends('v2.layouts.app')

@section('titlepage', 'Slip Gaji')
@section('page_title', 'Penerbitan Slip Gaji')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $tahunBulanan = request('tahun', date('Y'));
    $tahunHarian = request('tahun_harian', date('Y'));
    $totalBulanan = $slipgaji->count();
    $totalHarian = $slipgaji_harian->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTIONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-file-certificate text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data & Penerbitan Slip Gaji</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola status penerbitan slip gaji bulanan & harian untuk portal karyawan
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-cash text-sm"></i>
                    <span>Payroll</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Slip Gaji</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('slipgaji.create')
                    <!-- Tambah Slip Bulanan -->
                    <button type="button" id="btnCreateBulanan" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Buat Slip Bulanan</span>
                    </button>

                    <!-- Tambah Slip Harian -->
                    <button type="button" id="btnCreateHarian" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 text-emerald-700 hover:bg-emerald-50 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                        <i class="ti ti-calendar-plus text-sm"></i>
                        <span>Buat Slip Harian</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. TWO-COLUMN DUAL CARD LAYOUT (BULANAN & HARIAN) ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- ================= KOLOM KIRI: SLIP GAJI BULANAN (5 COLS) ================= -->
        <div class="lg:col-span-5 space-y-3">
            
            <!-- Filter Bar Bulanan Standar V2 -->
            <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
                <form action="{{ route('slipgaji.index') }}" method="GET" class="space-y-3">
                    <input type="hidden" name="tahun_harian" value="{{ request('tahun_harian') }}">
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                                <i class="ti ti-calendar text-sm"></i>
                            </span>
                            <select name="tahun" id="filter_tahun_bulanan" class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                                <option value="">Semua Tahun</option>
                                @for ($t = $start_year; $t <= date('Y'); $t++)
                                    <option value="{{ $t }}" {{ ($tahunBulanan == $t || (empty(request('tahun')) && date('Y') == $t)) ? 'selected' : '' }}>
                                        Tahun {{ $t }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit" 
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95 whitespace-nowrap"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-filter text-sm"></i>
                            <span>Filter</span>
                        </button>
                        @if(request()->has('tahun') && request('tahun') != '')
                            <a href="{{ route('slipgaji.index', ['tahun_harian' => request('tahun_harian')]) }}" 
                               class="inline-flex items-center justify-center p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs cursor-pointer"
                               title="Reset Filter Bulanan">
                                <i class="ti ti-refresh text-sm"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Header Section Bulanan -->
            <div class="flex items-center justify-between px-1">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                    <i class="ti ti-calendar text-sm text-emerald-600"></i>
                    <span>Slip Gaji Bulanan</span>
                </h3>
                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    {{ $totalBulanan }} Periode
                </span>
            </div>

            <!-- List Cards Bulanan -->
            <div class="space-y-2.5">
                @forelse ($slipgaji as $d)
                    <div class="bg-white rounded-2xl p-3.5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-slate-300 transition-all duration-200 group">
                        <div class="flex items-center justify-between gap-3">
                            
                            <!-- Left: Icon & Info -->
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $d->status == 1 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' : 'bg-amber-50 text-amber-700 border border-amber-200/80' }}">
                                    <i class="ti ti-calendar-event text-lg"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-xs font-extrabold text-slate-900 truncate">
                                            {{ getNamabulan($d->bulan) }} {{ $d->tahun }}
                                        </h4>
                                        @if ($d->status == 1)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Live
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Draft
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                                        <span class="font-mono text-slate-700 font-bold">{{ $d->kode_slip_gaji }}</span>
                                        <span>&bull;</span>
                                        @if ($d->periodeGaji)
                                            <span class="inline-flex items-center gap-1 text-sky-700 font-semibold truncate">
                                                <i class="ti ti-clock text-xs"></i>
                                                {{ $d->periodeGaji->nama_periode }}
                                            </span>
                                        @else
                                            <span class="text-slate-500">Default Global</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Actions -->
                            <div class="flex items-center gap-1 shrink-0">
                                @can('slipgaji.edit')
                                    <button type="button" 
                                            class="btnEditBulanan p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                            kode_slip_gaji="{{ Crypt::encrypt($d->kode_slip_gaji) }}" 
                                            title="Edit Slip Bulanan">
                                        <i class="ti ti-edit text-xs"></i>
                                    </button>
                                @endcan
                                @can('slipgaji.delete')
                                    <form method="POST" action="{{ route('slipgaji.delete', Crypt::encrypt($d->kode_slip_gaji)) }}" class="formDelete m-0 inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btnDeleteBulanan p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                                title="Hapus Slip Bulanan">
                                            <i class="ti ti-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white rounded-2xl border border-slate-200/90 shadow-2xs text-slate-400">
                        <i class="ti ti-folder-off text-3xl text-slate-300 mb-1 d-block"></i>
                        <p class="text-xs font-semibold text-slate-500">Tidak ada data slip bulanan</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ================= KOLOM KANAN: SLIP GAJI HARIAN (7 COLS) ================= -->
        <div class="lg:col-span-7 space-y-3">
            
            <!-- Filter Bar Harian Standar V2 -->
            <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
                <form action="{{ route('slipgaji.index') }}" method="GET" class="space-y-3">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                                <i class="ti ti-calendar text-sm"></i>
                            </span>
                            <select name="tahun_harian" id="filter_tahun_harian" class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                                <option value="">Semua Tahun</option>
                                @for ($t = $start_year; $t <= date('Y'); $t++)
                                    <option value="{{ $t }}" {{ ($tahunHarian == $t || (empty(request('tahun_harian')) && date('Y') == $t)) ? 'selected' : '' }}>
                                        Tahun {{ $t }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit" 
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95 whitespace-nowrap"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-filter text-sm"></i>
                            <span>Filter</span>
                        </button>
                        @if(request()->has('tahun_harian') && request('tahun_harian') != '')
                            <a href="{{ route('slipgaji.index', ['tahun' => request('tahun')]) }}" 
                               class="inline-flex items-center justify-center p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs cursor-pointer"
                               title="Reset Filter Harian">
                                <i class="ti ti-refresh text-sm"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Header Section Harian -->
            <div class="flex items-center justify-between px-1">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                    <i class="ti ti-calendar-stats text-sm text-sky-600"></i>
                    <span>Slip Gaji Harian (Upah Lepas)</span>
                </h3>
                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    {{ $totalHarian }} Batch
                </span>
            </div>

            <!-- List Cards Harian -->
            <div class="space-y-2.5">
                @forelse ($slipgaji_harian as $d)
                    <div class="bg-white rounded-2xl p-3.5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-slate-300 transition-all duration-200 group">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            
                            <!-- Left: Batch Icon & Details -->
                            <div class="flex items-start sm:items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $d->status == 1 ? 'bg-sky-50 text-sky-700 border border-sky-200/80' : 'bg-amber-50 text-amber-700 border border-amber-200/80' }}">
                                    <i class="ti ti-receipt-2 text-lg"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-xs font-mono font-extrabold text-slate-900">
                                            {{ $d->kode_slip_gaji_harian }}
                                        </h4>
                                        @if ($d->status == 1)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Live
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Draft
                                            </span>
                                        @endif
                                        <button type="button" 
                                                class="btnShowDetailHarian inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200 transition cursor-pointer"
                                                kode_slip="{{ Crypt::encrypt($d->kode_slip_gaji_harian) }}"
                                                title="Lihat & Cetak Rincian Karyawan">
                                            <i class="ti ti-users text-xs"></i>
                                            <span>{{ $d->detail_count }} Karyawan</span>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                                        <span>Tgl: <strong class="text-slate-700">{{ $d->tanggal_slip ? date('d/m/Y', strtotime($d->tanggal_slip)) : '-' }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Periode: <strong class="text-slate-700">{{ date('d/m/Y', strtotime($d->dari)) }} - {{ date('d/m/Y', strtotime($d->sampai)) }}</strong></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Actions -->
                            <div class="flex items-center justify-end gap-1 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                @can('slipgaji.edit')
                                    <button type="button" 
                                            class="btnEditHarian p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                            kode_slip="{{ Crypt::encrypt($d->kode_slip_gaji_harian) }}" 
                                            title="Edit Slip Harian">
                                        <i class="ti ti-edit text-xs"></i>
                                    </button>
                                @endcan
                                @can('slipgaji.delete')
                                    <form method="POST" action="{{ route('slipgajiharian.delete', Crypt::encrypt($d->kode_slip_gaji_harian)) }}" class="formDelete m-0 inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btnDeleteHarian p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                                title="Hapus Slip Harian">
                                            <i class="ti ti-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white rounded-2xl border border-slate-200/90 shadow-2xs text-slate-400">
                        <i class="ti ti-folder-off text-3xl text-slate-300 mb-1 d-block"></i>
                        <p class="text-xs font-semibold text-slate-500">Tidak ada data slip harian</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- ================= MODAL BULANAN STANDAR V2 ================= -->
<div id="modalBulanan" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Modal Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-file-certificate text-lg" id="modalBulananIcon"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="modalBulananTitle">
                        Slip Gaji Bulanan
                    </h3>
                    <p class="text-[11px] text-slate-500" id="modalBulananSubtitle">
                        Formulir penerbitan slip gaji bulanan
                    </p>
                </div>
            </div>
            <button type="button" 
                    class="btnCloseModalBulanan w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="modalBulananBody">
            <div class="py-12 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                <p class="text-xs font-semibold">Memuat formulir...</p>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL HARIAN STANDAR V2 ================= -->
<div id="modalHarian" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Modal Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-calendar-plus text-lg" id="modalHarianIcon"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="modalHarianTitle">
                        Slip Gaji Harian
                    </h3>
                    <p class="text-[11px] text-slate-500" id="modalHarianSubtitle">
                        Formulir penerbitan slip upah harian / lepas
                    </p>
                </div>
            </div>
            <button type="button" 
                    class="btnCloseModalHarian w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="modalHarianBody">
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
        // === MODAL BULANAN ===
        function showModalBulanan(title, subtitle, icon, url) {
            $('#modalBulananTitle').text(title);
            $('#modalBulananSubtitle').text(subtitle || 'Formulir penerbitan slip gaji bulanan');
            $('#modalBulananIcon').attr('class', 'ti ' + (icon || 'ti-file-certificate') + ' text-lg');
            $('#modalBulananBody').html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat formulir...</p>
                </div>
            `);
            $('#modalBulanan').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            $('#modalBulananBody').load(url, function(response, status, xhr) {
                if (status == "error") {
                    $('#modalBulananBody').html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat formulir.</p>
                            <p class="mt-1">${xhr.responseText || 'Terjadi kesalahan saat memuat data.'}</p>
                        </div>
                    `);
                }
            });
        }

        function closeModalBulanan() {
            $('#modalBulanan').addClass('hidden');
            $('body').removeClass('overflow-hidden');
            $('#modalBulananBody').html('');
        }

        $('#btnCreateBulanan').click(function(e) {
            e.preventDefault();
            showModalBulanan('Buat Slip Gaji Bulanan', 'Pilih bulan dan tahun untuk rilis slip gaji', 'ti-plus', '{{ route("slipgaji.create") }}');
        });

        $(document).on('click', '.btnEditBulanan', function(e) {
            e.preventDefault();
            let kode = $(this).attr('kode_slip_gaji');
            showModalBulanan('Edit Slip Gaji Bulanan', 'Ubah status atau konfigurasi periode slip', 'ti-edit', `/slipgaji/${kode}/edit`);
        });

        $('.btnCloseModalBulanan').click(function() {
            closeModalBulanan();
        });

        // === MODAL HARIAN ===
        function showModalHarian(title, subtitle, icon, url) {
            $('#modalHarianTitle').text(title);
            $('#modalHarianSubtitle').text(subtitle || 'Formulir penerbitan slip upah harian / lepas');
            $('#modalHarianIcon').attr('class', 'ti ' + (icon || 'ti-calendar-plus') + ' text-lg');
            $('#modalHarianBody').html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat formulir...</p>
                </div>
            `);
            $('#modalHarian').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            $('#modalHarianBody').load(url, function(response, status, xhr) {
                if (status == "error") {
                    $('#modalHarianBody').html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat formulir.</p>
                            <p class="mt-1">${xhr.responseText || 'Terjadi kesalahan saat memuat data.'}</p>
                        </div>
                    `);
                }
            });
        }

        function closeModalHarian() {
            $('#modalHarian').addClass('hidden');
            $('body').removeClass('overflow-hidden');
            $('#modalHarianBody').html('');
        }

        $('#btnCreateHarian').click(function(e) {
            e.preventDefault();
            showModalHarian('Buat Slip Gaji Harian', 'Generate batch slip gaji harian lepas per periode', 'ti-plus', '{{ route("slipgajiharian.create") }}');
        });

        $(document).on('click', '.btnEditHarian', function(e) {
            e.preventDefault();
            let kode = $(this).attr('kode_slip');
            showModalHarian('Edit Slip Gaji Harian', 'Perbarui status atau rentang tanggal slip harian', 'ti-edit', `/slipgajiharian/${kode}/edit`);
        });

        $(document).on('click', '.btnShowDetailHarian', function(e) {
            e.preventDefault();
            let kode = $(this).attr('kode_slip');
            showModalHarian('Rincian & Cetak Karyawan Slip Harian', 'Daftar penerima slip gaji harian & tombol cetak', 'ti-users', `/slipgajiharian/${kode}/show`);
        });

        $('.btnCloseModalHarian').click(function() {
            closeModalHarian();
        });

        // Close when click backdrop
        $('#modalBulanan, #modalHarian').click(function(e) {
            if (e.target === this) {
                closeModalBulanan();
                closeModalHarian();
            }
        });

        // ESC Key to close modal
        $(document).keydown(function(e) {
            if (e.key === 'Escape') {
                if (!$('#modalBulanan').hasClass('hidden')) closeModalBulanan();
                if (!$('#modalHarian').hasClass('hidden')) closeModalHarian();
            }
        });

        // === DELETE CONFIRMATION ===
        $(document).on('click', '.btnDeleteBulanan, .btnDeleteHarian', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: 'Data slip gaji yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0a5c41',
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
