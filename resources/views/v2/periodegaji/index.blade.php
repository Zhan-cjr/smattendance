@extends('v2.layouts.app')

@section('titlepage', 'Periode Gaji')
@section('page_title', 'Periode Gaji')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalPeriode = $periode_gaji->count();
    $totalKaryawan = $periode_gaji->sum('karyawan_aktif_count');
    $periodeAktifCount = $periode_gaji->where('is_active', true)->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-calendar-time text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data & Konfigurasi Periode Gaji</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola multi-periode siklus penggajian, cut-off absensi, dan penempatan karyawan
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
                <span class="font-bold text-slate-800">Periode Gaji</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('periodegaji.riwayatmutasi') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 text-slate-700 hover:bg-slate-50 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                    <i class="ti ti-history text-sm text-slate-500"></i>
                    <span>Riwayat Mutasi</span>
                </a>

                @can('periodegaji.create')
                    <button type="button" id="btnCreatePeriode" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Periode</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. EXECUTIVE METRICS CARD ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-calendar-time text-[190px]"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            <!-- Segment 1: Total Periode -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Periode
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Siklus
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalPeriode) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-calendar text-xs opacity-80"></i>
                        <span>Periode Gaji</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ $periodeAktifCount }} Aktif
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Total Karyawan Terdaftar -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Karyawan Terdaftar
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Pegawai
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalKaryawan) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-users text-xs opacity-80"></i>
                        <span>Alokasi Periode</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        Aktif
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Rata-Rata per Periode -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Rata-Rata Personel
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Rasio
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ $totalPeriode > 0 ? round($totalKaryawan / $totalPeriode, 1) : 0 }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-chart-bar text-xs opacity-80"></i>
                        <span>Per Siklus Gaji</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        Orang
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. FILTER & SEARCH TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 w-full">
            <!-- Search Input (Client side filter for fast responsiveness) -->
            <div class="flex-1 min-w-0 w-full relative">
                <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input type="text" 
                       id="searchInput" 
                       placeholder="Cari kode atau nama periode gaji..." 
                       class="w-full pl-9 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs text-slate-500 font-medium">
                    Total: <strong class="text-slate-800 font-bold" id="counterText">{{ $totalPeriode }}</strong> Periode
                </span>
            </div>
        </div>
    </div>

    <!-- ================= 4. LIST CARD DATA PERIODE GAJI ================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="periodeCardsContainer">
        @forelse ($periode_gaji as $item)
            @php
                $metodeText = 'Satu Bulan Sama (1 s/d 31)';
                $metodeBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                if ($item->metode_lintas_bulan == 1) {
                    $metodeText = 'Bulan Lalu ke Bulan Berjalan';
                    $metodeBadge = 'bg-amber-50 text-amber-700 border-amber-200';
                } elseif ($item->metode_lintas_bulan == 2) {
                    $metodeText = 'Bulan Berjalan ke Bulan Depan';
                    $metodeBadge = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                }
            @endphp
            <div class="periode-card bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-slate-300 transition-all duration-200 flex flex-col justify-between group"
                 data-nama="{{ strtolower($item->nama_periode) }}"
                 data-kode="{{ strtolower($item->kode_periode_gaji) }}">
                
                <div>
                    <!-- Top Bar Card: Icon + Kode & Status -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 shadow-2xs text-white"
                                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-calendar-time text-xl"></i>
                            </div>
                            <div>
                                <span class="font-mono text-xs font-black text-slate-800 px-2 py-0.5 bg-slate-100 rounded-md border border-slate-200">
                                    {{ $item->kode_periode_gaji }}
                                </span>
                                <h3 class="text-sm font-black text-slate-900 mt-1 leading-snug group-hover:text-emerald-700 transition-colors">
                                    {{ $item->nama_periode }}
                                </h3>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if ($item->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Cut-Off & Siklus Info Box -->
                    <div class="mt-4 p-3 rounded-xl bg-slate-50/80 border border-slate-100 space-y-2">
                        <!-- Cut Off Range -->
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 flex items-center gap-1">
                                <i class="ti ti-calendar-event text-sm text-slate-400"></i>
                                <span>Rentang Cut-Off</span>
                            </span>
                            <span class="font-extrabold text-slate-800">
                                Tgl {{ $item->tanggal_dari }} s/d Tgl {{ $item->tanggal_sampai }}
                            </span>
                        </div>

                        <!-- Siklus Bulan -->
                        <div class="flex items-center justify-between text-xs pt-1.5 border-t border-slate-200/60">
                            <span class="text-slate-500 flex items-center gap-1">
                                <i class="ti ti-arrows-left-right text-sm text-slate-400"></i>
                                <span>Siklus</span>
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $metodeBadge }} truncate max-w-[170px]" title="{{ $metodeText }}">
                                {{ $metodeText }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Card: Anggota Button & Actions -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <!-- Link Setting Karyawan -->
                    @can('periodegaji.setting')
                        <a href="{{ route('periodegaji.setting', $item->kode_periode_gaji) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200/80 text-xs font-bold transition active:scale-95 cursor-pointer"
                           title="Kelola Karyawan pada Periode ini">
                            <i class="ti ti-users text-sm"></i>
                            <span>{{ $item->karyawan_aktif_count ?? 0 }} Karyawan</span>
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">
                            <i class="ti ti-users text-sm"></i>
                            <span>{{ $item->karyawan_aktif_count ?? 0 }} Karyawan</span>
                        </span>
                    @endcan

                    <!-- Action Buttons: Edit & Delete -->
                    <div class="flex items-center gap-1">
                        @can('periodegaji.edit')
                            <button type="button" 
                                    class="btnEditPeriode p-2 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                    data-kode="{{ $item->kode_periode_gaji }}" 
                                    title="Edit Periode Gaji">
                                <i class="ti ti-edit text-xs"></i>
                            </button>
                        @endcan

                        @can('periodegaji.delete')
                            <form action="{{ route('periodegaji.delete', $item->kode_periode_gaji) }}" method="POST" class="deleteform m-0 inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btnDeletePeriode p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                        title="Hapus Periode Gaji">
                                    <i class="ti ti-trash text-xs"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full p-12 text-center bg-white rounded-2xl border border-slate-200/90 shadow-2xs text-slate-400">
                <i class="ti ti-folder-off text-4xl text-slate-300 mb-2 d-block"></i>
                <p class="text-sm font-bold text-slate-700">Belum ada periode gaji yang dibuat</p>
                <p class="text-xs text-slate-400 mt-1">Klik tombol <strong>Tambah Periode</strong> di atas untuk membuat konfigurasi baru.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- ================= UNIVERSAL V2 MODAL UNTUK PERIODE GAJI ================= -->
<div id="modalUniversal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Modal Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-calendar-time text-lg" id="modalIcon"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="modalTitle">
                        Periode Gaji
                    </h3>
                    <p class="text-[11px] text-slate-500" id="modalSubtitle">
                        Konfigurasi siklus dan tanggal cut-off penggajian
                    </p>
                </div>
            </div>
            <button type="button" 
                    class="btnCloseModalUniversal w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="modalContent">
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
        // === MODAL UNIVERSAL V2 ===
        function openModalUniversal(title, subtitle, icon, url) {
            $('#modalTitle').text(title);
            $('#modalSubtitle').text(subtitle || 'Konfigurasi siklus penggajian');
            $('#modalIcon').attr('class', 'ti ' + (icon || 'ti-calendar-time') + ' text-lg');
            $('#modalContent').html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat formulir...</p>
                </div>
            `);
            $('#modalUniversal').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            $('#modalContent').load(url, function(response, status, xhr) {
                if (status == "error") {
                    $('#modalContent').html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat formulir.</p>
                            <p class="mt-1">${xhr.responseText || 'Terjadi kesalahan saat memuat data.'}</p>
                        </div>
                    `);
                }
            });
        }

        function closeModalUniversal() {
            $('#modalUniversal').addClass('hidden');
            $('body').removeClass('overflow-hidden');
            $('#modalContent').html('');
        }

        $('#btnCreatePeriode').click(function(e) {
            e.preventDefault();
            openModalUniversal('Tambah Periode Gaji', 'Konfigurasi cut-off dan siklus penggajian baru', 'ti-plus', '{{ route("periodegaji.create") }}');
        });

        $(document).on('click', '.btnEditPeriode', function(e) {
            e.preventDefault();
            let kode = $(this).data('kode');
            openModalUniversal('Edit Periode Gaji', 'Perbarui parameter siklus dan tanggal cut-off', 'ti-edit', `/periodegaji/${kode}/edit`);
        });

        $(document).on('click', '.btnCloseModalUniversal', function() {
            closeModalUniversal();
        });

        $('#modalUniversal').click(function(e) {
            if (e.target === this) closeModalUniversal();
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$('#modalUniversal').hasClass('hidden')) {
                closeModalUniversal();
            }
        });

        // === SEARCH FILTER REALTIME ===
        $('#searchInput').on('input', function() {
            let val = $(this).val().toLowerCase().trim();
            let visibleCount = 0;

            $('.periode-card').each(function() {
                let nama = $(this).data('nama') || '';
                let kode = $(this).data('kode') || '';
                if (nama.includes(val) || kode.includes(val)) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            $('#counterText').text(visibleCount);
        });

        // === DELETE CONFIRMATION ===
        $(document).on('click', '.btnDeletePeriode', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Periode Gaji?',
                text: 'Data periode gaji beserta penempatan karyawan terkait akan dihapus!',
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
