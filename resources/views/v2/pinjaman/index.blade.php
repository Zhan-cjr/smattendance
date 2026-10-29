@extends('v2.layouts.app')

@section('titlepage', 'Pinjaman Karyawan')
@section('page_title', 'Pinjaman Karyawan')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalPinjamanCount = $pinjaman->total();
    $activeTab = request('tab', 'pinjaman');
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-credit-card text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Manajemen Pinjaman Karyawan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola pengajuan pinjaman, jadwal cicilan, dan histori pemotongan angsuran payroll
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
                <span class="font-bold text-slate-800">Pinjaman</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('pinjaman.generate')
                    <a href="{{ route('pinjaman.generate') }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 text-slate-700 hover:bg-slate-50 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                        <i class="ti ti-settings-automation text-sm text-sky-600"></i>
                        <span>Generate Pembayaran Massal</span>
                    </a>
                @endcan

                @can('pinjaman.create')
                    <button type="button" id="btnCreatePinjamanV2" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Pinjaman</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. SEGMENTED TAB SWITCHER ================= -->
    <div class="flex items-center justify-start sm:justify-center">
        <div class="inline-flex p-1 bg-slate-200/70 rounded-2xl border border-slate-300/60 shadow-inner gap-1">
            <button type="button" id="tabBtnPinjaman" 
                    class="tab-btn px-5 py-2 rounded-xl text-xs font-black transition-all duration-200 flex items-center gap-2 {{ $activeTab == 'pinjaman' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <i class="ti ti-credit-card text-sm"></i>
                <span>Data Pinjaman</span>
            </button>
            <button type="button" id="tabBtnHistory" 
                    class="tab-btn px-5 py-2 rounded-xl text-xs font-black transition-all duration-200 flex items-center gap-2 {{ $activeTab == 'history' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <i class="ti ti-history text-sm"></i>
                <span>Histori Generate Massal</span>
            </button>
        </div>
    </div>

    <!-- ================= 3. TAB DATA PINJAMAN ================= -->
    <div id="tabContentPinjaman" class="{{ $activeTab == 'pinjaman' ? '' : 'hidden' }} space-y-4">
        
        <!-- Filter Bar Pinjaman -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <form action="{{ route('pinjaman.index') }}" method="GET" class="space-y-3">
                <input type="hidden" name="tab" value="pinjaman">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-2.5 sm:gap-3 items-center">
                    
                    <!-- Cari Nama Karyawan -->
                    <div class="md:col-span-4 relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                            <i class="ti ti-search text-sm"></i>
                        </span>
                        <input type="text" name="nama_karyawan" id="filter_nama_karyawan" 
                               value="{{ request('nama_karyawan') }}" 
                               placeholder="Cari Nama Karyawan..." 
                               class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 placeholder-slate-400 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                    </div>

                    <!-- Filter Cabang -->
                    <div class="md:col-span-2">
                        <select name="kode_cabang" id="filter_kode_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                    {{ $c->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Departemen -->
                    <div class="md:col-span-2">
                        <select name="kode_dept" id="filter_kode_dept" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                            <option value="">Semua Dept</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                    {{ $d->nama_dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="md:col-span-2">
                        <select name="status" id="filter_status" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="A" {{ request('status') == 'A' ? 'selected' : '' }}>Aktif</option>
                            <option value="L" {{ request('status') == 'L' ? 'selected' : '' }}>Lunas</option>
                            <option value="B" {{ request('status') == 'B' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>

                    <!-- Buttons: Filter & Reset -->
                    <div class="md:col-span-2 flex items-center gap-1.5">
                        <button type="submit" 
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-filter text-sm"></i>
                            <span>Filter</span>
                        </button>
                        @if (request()->hasAny(['nama_karyawan', 'kode_cabang', 'kode_dept', 'status']))
                            <a href="{{ route('pinjaman.index', ['tab' => 'pinjaman']) }}" 
                               class="inline-flex items-center justify-center p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs cursor-pointer"
                               title="Reset Filter">
                                <i class="ti ti-refresh text-sm"></i>
                            </a>
                        @endif
                    </div>

                </div>
            </form>

            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Menampilkan <strong>{{ $pinjaman->count() }}</strong> dari <strong>{{ $totalPinjamanCount }}</strong> total pinjaman</span>
            </div>
        </div>

        <!-- List Card Data Pinjaman -->
        <div class="space-y-3">
            @forelse ($pinjaman as $d)
                @php
                    $k = $d->karyawan;
                    $foto = $k->foto ?? null;
                    $fotoUrl = (!empty($foto) && Storage::disk('public')->exists('/karyawan/' . $foto))
                        ? getfotoKaryawan($foto)
                        : asset('assets/img/avatars/No_Image_Available.jpg');

                    $statusBadge = 'bg-sky-50 text-sky-700 border-sky-200';
                    $statusText = 'Aktif';
                    if ($d->status == 'L') {
                        $statusBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        $statusText = 'Lunas';
                    } elseif ($d->status == 'B') {
                        $statusBadge = 'bg-rose-50 text-rose-700 border-rose-200';
                        $statusText = 'Batal';
                    }
                @endphp
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-slate-300 transition-all duration-200 group">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        
                        <!-- Left: Profile Identity -->
                        <div class="flex items-center gap-3.5 min-w-0 lg:w-4/12">
                            <div class="relative shrink-0">
                                <img src="{{ $fotoUrl }}" alt="Foto" 
                                     class="w-12 h-12 rounded-xl object-cover border border-slate-200/80 shadow-2xs">
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs sm:text-sm font-black text-slate-900 truncate group-hover:text-emerald-700 transition-colors">
                                    {{ $k->nama_karyawan ?? '-' }}
                                </h4>
                                <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                    <span class="font-mono text-[10.5px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $d->nik }}
                                    </span>
                                    <span class="font-mono text-[10.5px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        {{ $d->no_pinjaman }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $statusBadge }}">
                                        {{ $statusText }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Middle: Financial Figures (Total, Dibayar, Sisa) -->
                        <div class="grid grid-cols-3 gap-2 lg:w-4/12 pt-2 sm:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-4 text-center">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Pokok</span>
                                <span class="text-xs font-black text-slate-800 block mt-0.5">
                                    Rp {{ number_format($d->jumlah_pinjaman, 0, ',', '.') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 block">Dibayar</span>
                                <span class="text-xs font-black text-emerald-700 block mt-0.5">
                                    Rp {{ number_format($d->total_dibayar, 0, ',', '.') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-500 block">Sisa Saldo</span>
                                <span class="text-xs font-black text-rose-600 block mt-0.5">
                                    Rp {{ number_format($d->sisa_pinjaman, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Right: Metadata & Actions -->
                        <div class="flex items-center justify-between lg:justify-end gap-3 lg:w-4/12 pt-2 sm:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-4">
                            <div class="text-left lg:text-right">
                                <span class="text-[11px] text-slate-500 flex items-center lg:justify-end gap-1 font-semibold">
                                    <i class="ti ti-calendar text-xs text-slate-400"></i>
                                    {{ date('d/m/Y', strtotime($d->tanggal_pinjaman)) }}
                                </span>
                                <span class="text-[10.5px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md mt-1 inline-block">
                                    {{ $d->jumlah_cicilan }} Bulan Angsuran
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-1 shrink-0">
                                <a href="{{ route('pinjaman.show', Crypt::encrypt($d->id)) }}" 
                                   class="p-2 rounded-xl bg-sky-50 text-sky-600 hover:bg-sky-100 border border-sky-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                   title="Lihat Detail & Angsuran">
                                    <i class="ti ti-eye text-xs"></i>
                                </a>

                                @if($d->total_dibayar == 0 && $d->status != 'B')
                                    @can('pinjaman.edit')
                                        <button type="button" 
                                                class="btnEditPinjaman p-2 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                                data-id="{{ Crypt::encrypt($d->id) }}" 
                                                title="Edit Pinjaman">
                                            <i class="ti ti-edit text-xs"></i>
                                        </button>
                                    @endcan

                                    @can('pinjaman.delete')
                                        <form action="{{ route('pinjaman.delete', Crypt::encrypt($d->id)) }}" method="POST" class="deleteform m-0 inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btnDeletePinjaman p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                                    title="Hapus Pinjaman">
                                                <i class="ti ti-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="p-12 text-center bg-white rounded-2xl border border-slate-200/90 shadow-2xs text-slate-400">
                    <i class="ti ti-folder-off text-4xl text-slate-300 mb-2 d-block"></i>
                    <p class="text-sm font-bold text-slate-700">Belum ada data pinjaman karyawan</p>
                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol <strong>Tambah Pinjaman</strong> untuk mendaftarkan pinjaman baru.</p>
                </div>
            @endforelse

            <!-- Pagination -->
            <div class="pt-2">
                {{ $pinjaman->links('v2.components.pagination') }}
            </div>
        </div>
    </div>

    <!-- ================= 4. TAB HISTORI GENERATE MASSAL ================= -->
    <div id="tabContentHistory" class="{{ $activeTab == 'history' ? '' : 'hidden' }} space-y-4">
        
        <!-- Filter Bar Histori -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <form action="{{ route('pinjaman.index') }}" method="GET" class="space-y-3">
                <input type="hidden" name="tab" value="history">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-2.5 sm:gap-3 items-center">
                    
                    <!-- Filter Cabang -->
                    <div class="md:col-span-3">
                        <select name="kode_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                    {{ $c->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Departemen -->
                    <div class="md:col-span-3">
                        <select name="kode_dept" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                    {{ $d->nama_dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Bulan -->
                    <div class="md:col-span-2">
                        <select name="bulan_search" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                            <option value="">Semua Bulan</option>
                            @foreach ($list_bulan as $b)
                                <option value="{{ $b['kode_bulan'] }}" {{ request('bulan_search') == $b['kode_bulan'] ? 'selected' : '' }}>
                                    {{ $b['nama_bulan'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Tahun -->
                    <div class="md:col-span-2">
                        <select name="tahun_search" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                            <option value="">Semua Tahun</option>
                            @for ($i = date('Y'); $i >= $start_year; $i--)
                                <option value="{{ $i }}" {{ request('tahun_search') == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Buttons: Filter & Reset -->
                    <div class="md:col-span-2 flex items-center gap-1.5">
                        <button type="submit" 
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-filter text-sm"></i>
                            <span>Filter</span>
                        </button>
                        @if (request()->hasAny(['kode_cabang', 'kode_dept', 'bulan_search', 'tahun_search']))
                            <a href="{{ route('pinjaman.index', ['tab' => 'history']) }}" 
                               class="inline-flex items-center justify-center p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs cursor-pointer"
                               title="Reset Filter">
                                <i class="ti ti-refresh text-sm"></i>
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>

        <!-- List Card Histori Generate -->
        <div class="space-y-3">
            @forelse ($history_generate as $h)
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs hover:shadow-md transition-all">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        
                        <!-- Left: Batch Code & Icon -->
                        <div class="flex items-center gap-3.5 min-w-0 lg:w-4/12">
                            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-700 border border-sky-200/80 flex items-center justify-center shrink-0">
                                <i class="ti ti-history text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-slate-400 block">Kode Batch Generate</span>
                                <h4 class="text-xs sm:text-sm font-mono font-black text-slate-900 truncate">
                                    {{ $h->kode_generate ?? 'BATCH-' . $h->id }}
                                </h4>
                            </div>
                        </div>

                        <!-- Middle: Periode Pemotongan -->
                        <div class="lg:w-4/12 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-4 pt-2 sm:pt-0">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Periode Pemotongan Gaji</span>
                            <span class="text-xs sm:text-sm font-black text-slate-800 block mt-0.5">
                                {{ config('global.nama_bulan')[(int)$h->bulan] }} {{ $h->tahun }}
                            </span>
                            <span class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                <i class="ti ti-calendar-event text-xs text-slate-400"></i>
                                Eksekusi: {{ date('d/m/Y H:i', strtotime($h->created_at)) }}
                            </span>
                        </div>

                        <!-- Right: Operator User & Actions -->
                        <div class="flex items-center justify-between lg:justify-end gap-3 lg:w-4/12 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-4 pt-2 sm:pt-0">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Diproses Oleh</span>
                                <span class="text-xs font-extrabold text-slate-700 block mt-0.5">
                                    {{ $h->user?->name ?? 'System User' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" 
                                        class="btnDetailHistoryV2 inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200/80 text-xs font-bold transition active:scale-95 cursor-pointer shadow-2xs"
                                        data-id="{{ Crypt::encrypt($h->id) }}"
                                        title="Rincian Batch Generate">
                                    <i class="ti ti-eye text-xs"></i>
                                    <span>Rincian</span>
                                </button>

                                @can('pinjaman.generate')
                                    @if ($h->id == $latest_history_id)
                                        <form action="{{ route('pinjaman.history.delete', Crypt::encrypt($h->id)) }}" method="POST" class="deleteform m-0 inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btnDeleteHistoryV2 p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                                    title="Hapus & Batalkan Generate Ini">
                                                <i class="ti ti-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" 
                                                disabled
                                                class="p-2 rounded-xl bg-slate-100 text-slate-300 border border-slate-200 cursor-not-allowed shadow-2xs opacity-60"
                                                title="Hanya batch generate periode terbaru yang dapat dihapus. Hapus periode yang lebih baru terlebih dahulu.">
                                            <i class="ti ti-trash text-xs"></i>
                                        </button>
                                    @endif
                                @endcan
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="p-12 text-center bg-white rounded-2xl border border-slate-200/90 shadow-2xs text-slate-400">
                    <i class="ti ti-history-off text-4xl text-slate-300 mb-2 d-block"></i>
                    <p class="text-sm font-bold text-slate-700">Belum ada histori pemotongan pinjaman massal</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- ================= UNIVERSAL MODAL PINJAMAN V2 (CREATE & EDIT) ================= -->
<div id="modalPinjamanUniversalV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative shrink-0"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-credit-card text-lg" id="modalPinjamanIcon"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="modalPinjamanTitle">
                        Pinjaman Karyawan
                    </h3>
                    <p class="text-[11px] text-slate-500" id="modalPinjamanSubtitle">
                        Formulir pengajuan & rencana cicilan pinjaman
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseModalPinjaman w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="modalPinjamanContent">
            <div class="py-12 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                <p class="text-xs font-semibold">Memuat formulir...</p>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL LOOKUP KARYAWAN ================= -->
<div id="modalLookupKaryawanV2" class="fixed inset-0 z-[70] hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4" style="z-index: 70;">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto relative" style="z-index: 71;">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative shrink-0"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-users text-lg"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight">
                        Pilih Data Karyawan
                    </h3>
                    <p class="text-[11px] text-slate-500">
                        Pilih karyawan pemohon pinjaman
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseModalLookup w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="loadLookupKaryawanV2">
            <div class="py-12 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                <p class="text-xs font-semibold">Memuat daftar karyawan...</p>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL HISTORI GENERATE ================= -->
<div id="modalDetailHistoryV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative shrink-0"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-history text-lg"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight">
                        Rincian Pemotongan Massal
                    </h3>
                    <p class="text-[11px] text-slate-500">
                        Daftar karyawan dan angsuran yang dipotong pada batch ini
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseModalHistoryDetail w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="loadDetailHistoryContent">
            <div class="py-12 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                <p class="text-xs font-semibold">Memuat rincian...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        // === TAB SWITCHING LOGIC ===
        $('#tabBtnPinjaman').click(function() {
            $(this).addClass('bg-white text-slate-900 shadow-sm').removeClass('text-slate-600');
            $('#tabBtnHistory').removeClass('bg-white text-slate-900 shadow-sm').addClass('text-slate-600');
            $('#tabContentPinjaman').removeClass('hidden');
            $('#tabContentHistory').addClass('hidden');
        });

        $('#tabBtnHistory').click(function() {
            $(this).addClass('bg-white text-slate-900 shadow-sm').removeClass('text-slate-600');
            $('#tabBtnPinjaman').removeClass('bg-white text-slate-900 shadow-sm').addClass('text-slate-600');
            $('#tabContentHistory').removeClass('hidden');
            $('#tabContentPinjaman').addClass('hidden');
        });

        // === MODAL PINJAMAN UNIVERSAL ===
        function showModalPinjaman(title, subtitle, icon, url) {
            $('#modalPinjamanTitle').text(title);
            $('#modalPinjamanSubtitle').text(subtitle || 'Formulir pinjaman karyawan');
            $('#modalPinjamanIcon').attr('class', 'ti ' + (icon || 'ti-credit-card') + ' text-lg');
            $('#modalPinjamanContent').html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat formulir...</p>
                </div>
            `);
            $('#modalPinjamanUniversalV2').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            $('#modalPinjamanContent').load(url, function(response, status, xhr) {
                if (status == "error") {
                    $('#modalPinjamanContent').html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold text-center">
                            Gagal memuat formulir pinjaman.
                        </div>
                    `);
                }
            });
        }

        function closeModalPinjaman() {
            $('#modalPinjamanUniversalV2').addClass('hidden');
            $('body').removeClass('overflow-hidden');
            $('#modalPinjamanContent').html('');
        }

        $('#btnCreatePinjamanV2').click(function(e) {
            e.preventDefault();
            showModalPinjaman('Tambah Pinjaman Karyawan', 'Daftarkan pengajuan pinjaman baru dan susun rencana cicilan', 'ti-plus', '{{ route("pinjaman.create") }}');
        });

        $(document).on('click', '.btnEditPinjaman', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            showModalPinjaman('Edit Pinjaman Karyawan', 'Perbarui parameter jumlah pinjaman atau tenor angsuran', 'ti-edit', `/pinjaman/${id}/edit`);
        });

        $(document).on('click', '.btnCloseModalPinjaman', closeModalPinjaman);

        // === LOOKUP KARYAWAN MODAL ===
        $(document).on('click', '.btnCloseModalLookup', function() {
            $('#modalLookupKaryawanV2').addClass('hidden');
        });

        // Pilih Karyawan dari Modal Lookup
        $(document).off('click', '.btnPilihKaryawan').on('click', '.btnPilihKaryawan', function(e) {
            e.preventDefault();
            let nik = $(this).attr('data-nik');
            let nama = $(this).attr('data-nama');
            
            if (nik && nama) {
                $("#v2_nik").val(nik);
                $("#v2_nama_karyawan").val(nik + ' - ' + nama);
                $("#modalLookupKaryawanV2").addClass('hidden');
            }
        });

        // === DETAIL HISTORI GENERATE ===
        $(document).on('click', '.btnDetailHistoryV2', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $('#modalDetailHistoryV2').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            $('#loadDetailHistoryContent').html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat rincian...</p>
                </div>
            `);
            $('#loadDetailHistoryContent').load(`/pinjaman/history/${id}`);
        });

        $(document).on('click', '.btnCloseModalHistoryDetail', function() {
            $('#modalDetailHistoryV2').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        });

        // Backdrop & ESC Close Handlers
        $('#modalPinjamanUniversalV2, #modalLookupKaryawanV2, #modalDetailHistoryV2').click(function(e) {
            if (e.target === this) {
                $(this).addClass('hidden');
                $('body').removeClass('overflow-hidden');
            }
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape') {
                $('#modalLookupKaryawanV2').addClass('hidden');
                $('#modalPinjamanUniversalV2').addClass('hidden');
                $('#modalDetailHistoryV2').addClass('hidden');
                $('body').removeClass('overflow-hidden');
            }
        });

        // Delete Confirmations
        $(document).on('click', '.btnDeletePinjaman, .btnDeleteHistoryV2', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
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
