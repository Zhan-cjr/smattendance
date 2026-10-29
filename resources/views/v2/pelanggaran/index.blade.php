@extends('v2.layouts.app')

@section('titlepage', 'Surat Peringatan & Pelanggaran')
@section('page_title', 'Surat Peringatan (SP)')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTIONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-file-alert text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Surat Peringatan & Pelanggaran</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola pencatatan sanksi kedisiplinan, riwayat surat peringatan (SP), butir pelanggaran, dan cetak dokumen resmi SP
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
                    <i class="ti ti-shield-alert text-sm"></i>
                    <span>Kedisiplinan</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Pelanggaran</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('pelanggaran.create')
                    <button type="button" id="btnCreatePelanggaran" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Pelanggaran</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER BAR STANDAR V2 (TANPA STATISTIC CARD) ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('pelanggaran.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 sm:gap-3 items-center">
                
                <!-- Select Karyawan (Jika bukan role karyawan) -->
                @if (!auth()->user()->hasRole('karyawan') && isset($karyawans))
                    <div class="md:col-span-4">
                        <select name="nik" id="filter_nik_pelanggaran" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer select2NikFilter">
                            <option value="">Semua Karyawan</option>
                            @foreach ($karyawans as $karyawan)
                                <option value="{{ $karyawan->nik }}" {{ request('nik') == $karyawan->nik ? 'selected' : '' }}>
                                    {{ $karyawan->nik_show ?? $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Tanggal Dari -->
                <div class="{{ auth()->user()->hasRole('karyawan') ? 'md:col-span-5' : 'md:col-span-3' }} relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-sm"></i>
                    </span>
                    <input type="text" name="dari" id="filter_dari" 
                           value="{{ request('dari') }}" 
                           placeholder="Tanggal Dari" 
                           class="w-full pl-9 pr-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 placeholder-slate-400 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition flatpickr-filter"
                           autocomplete="off">
                </div>

                <!-- Tanggal Sampai -->
                <div class="{{ auth()->user()->hasRole('karyawan') ? 'md:col-span-5' : 'md:col-span-3' }} relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-sm"></i>
                    </span>
                    <input type="text" name="sampai" id="filter_sampai" 
                           value="{{ request('sampai') }}" 
                           placeholder="Tanggal Sampai" 
                           class="w-full pl-9 pr-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 placeholder-slate-400 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition flatpickr-filter"
                           autocomplete="off">
                </div>

                <!-- Actions Button (Filter & Reset) -->
                <div class="md:col-span-2 flex items-center gap-1.5">
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if (request()->hasAny(['nik', 'dari', 'sampai', 'nama_karyawan']))
                        <a href="{{ route('pelanggaran.index') }}" 
                           class="inline-flex items-center justify-center p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs cursor-pointer"
                           title="Reset Filter">
                            <i class="ti ti-refresh text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    <!-- ================= 3. LIST CARD MODERN V2 ================= -->
    <div class="space-y-3">
        @forelse ($pelanggaran as $item)
            @php
                $jenisSp = strtoupper(trim($item->jenis_sp ?? ''));
                $badgeStyle = 'bg-slate-100 text-slate-700 border-slate-200';
                $barColor = 'bg-slate-400';
                $badgeLabel = $jenisSp;

                if ($jenisSp === 'ST') {
                    $badgeStyle = 'bg-sky-50 text-sky-700 border-sky-200';
                    $barColor = 'bg-sky-500';
                    $badgeLabel = 'Surat Teguran (ST)';
                } elseif ($jenisSp === 'SP1') {
                    $badgeStyle = 'bg-amber-50 text-amber-700 border-amber-200';
                    $barColor = 'bg-amber-500';
                    $badgeLabel = 'Surat Peringatan 1 (SP 1)';
                } elseif ($jenisSp === 'SP2') {
                    $badgeStyle = 'bg-orange-50 text-orange-700 border-orange-200';
                    $barColor = 'bg-orange-500';
                    $badgeLabel = 'Surat Peringatan 2 (SP 2)';
                } elseif ($jenisSp === 'SP3') {
                    $badgeStyle = 'bg-rose-50 text-rose-700 border-rose-200';
                    $barColor = 'bg-rose-600';
                    $badgeLabel = 'Surat Peringatan 3 (SP 3)';
                }

                // Parse Keterangan List
                $keteranganList = is_string($item->keterangan) 
                    ? json_decode($item->keterangan, true) 
                    : ($item->keterangan ?? []);
                if (empty($keteranganList)) {
                    $keteranganList = !empty($item->keterangan) ? [$item->keterangan] : [];
                }
                if (!is_array($keteranganList)) {
                    $keteranganList = [$keteranganList];
                }

                // Status Masa Berlaku
                $today = \Carbon\Carbon::now()->startOfDay();
                $dariDate = !empty($item->dari) ? \Carbon\Carbon::parse($item->dari)->startOfDay() : null;
                $sampaiDate = !empty($item->sampai) ? \Carbon\Carbon::parse($item->sampai)->endOfDay() : null;
                $isAktif = false;
                if ($dariDate && $sampaiDate) {
                    $isAktif = $today->between($dariDate, $sampaiDate);
                }
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-slate-300 transition-all duration-200 overflow-hidden group">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        
                        <!-- Col 1: Karyawan & Dokumen Identitas -->
                        <div class="flex items-start gap-3.5 min-w-0 lg:w-4/12">
                            <!-- Avatar / SP Icon -->
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 shadow-2xs font-black text-sm {{ $badgeStyle }}">
                                <i class="ti ti-file-alert text-xl"></i>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-slate-900 text-sm truncate hover:text-emerald-700 transition">
                                        {{ $item->nama_karyawan ?? 'Karyawan Tidak Diketahui' }}
                                    </h3>
                                    <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/70">
                                        {{ $item->nik_show ?? $item->nik }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                    @if (!empty($item->nama_jabatan))
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-slate-100/90 px-2 py-0.5 rounded-md border border-slate-200/60">
                                            <i class="ti ti-briefcase text-[11px]"></i>
                                            <span>{{ $item->nama_jabatan }}</span>
                                        </span>
                                    @endif
                                    @if (!empty($item->nama_dept))
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                                            <i class="ti ti-building text-[11px]"></i>
                                            <span>{{ $item->nama_dept }}</span>
                                        </span>
                                    @endif
                                    @if (!empty($item->nama_cabang))
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200/50">
                                            <i class="ti ti-map-pin text-[11px]"></i>
                                            <span>{{ $item->nama_cabang }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Col 2: Info Surat & Poin Pelanggaran -->
                        <div class="lg:w-5/12 border-t lg:border-t-0 lg:border-l lg:border-r border-slate-100 pt-3 lg:pt-0 lg:px-4 flex flex-col justify-center gap-2">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-black px-2.5 py-1 rounded-lg border {{ $badgeStyle }}">
                                        <i class="ti ti-shield-alert text-xs"></i>
                                        <span>{{ $badgeLabel }}</span>
                                    </span>
                                    @if (!empty($item->no_dokumen))
                                        <span class="text-xs font-bold text-slate-700 truncate" title="{{ $item->no_dokumen }}">
                                            No: {{ $item->no_dokumen }}
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400">
                                            ID: {{ $item->no_sp }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Status Aktif / Berakhir -->
                                @if ($isAktif)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Masa Berlaku Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Masa Berlaku Selesai</span>
                                    </span>
                                @endif
                            </div>

                            <!-- Periode & Tanggal -->
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 font-medium">
                                <div class="flex items-center gap-1.5">
                                    <i class="ti ti-calendar text-slate-400"></i>
                                    <span>Tgl Terbit: <strong class="text-slate-800">{{ !empty($item->tanggal) ? date('d M Y', strtotime($item->tanggal)) : '-' }}</strong></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="ti ti-calendar-event text-slate-400"></i>
                                    <span>Periode: <strong class="text-slate-800">{{ !empty($item->dari) ? date('d/m/Y', strtotime($item->dari)) : '-' }} s.d {{ !empty($item->sampai) ? date('d/m/Y', strtotime($item->sampai)) : '-' }}</strong></span>
                                </div>
                            </div>

                            <!-- Preview Pelanggaran Ringkas -->
                            @if (!empty($keteranganList) && count($keteranganList) > 0 && !empty($keteranganList[0]))
                                <div class="text-xs text-slate-500 line-clamp-1 bg-slate-50/80 px-2.5 py-1.5 rounded-lg border border-slate-200/50 flex items-center gap-1.5">
                                    <i class="ti ti-alert-triangle text-amber-500 text-xs shrink-0"></i>
                                    <span class="truncate">{{ $keteranganList[0] }}</span>
                                    @if (count($keteranganList) > 1)
                                        <span class="text-[10px] font-bold text-slate-400 shrink-0">+{{ count($keteranganList) - 1 }} poin lainnya</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Col 3: Actions -->
                        <div class="lg:w-3/12 flex items-center justify-end gap-1.5 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                            @can('pelanggaran.index')
                                <!-- Cetak SP -->
                                <a href="{{ route('pelanggaran.print', Crypt::encrypt($item->no_sp)) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs border border-slate-300/80 shadow-2xs transition active:scale-95 cursor-pointer"
                                   title="Cetak Surat Peringatan">
                                    <i class="ti ti-printer text-sm text-slate-500"></i>
                                    <span class="hidden sm:inline">Cetak</span>
                                </a>

                                <!-- Show Detail -->
                                <button type="button" 
                                        class="btnShowPelanggaran inline-flex items-center gap-1 px-2.5 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold rounded-xl text-xs border border-sky-200 shadow-2xs transition active:scale-95 cursor-pointer"
                                        data-nosp="{{ Crypt::encrypt($item->no_sp) }}"
                                        title="Lihat Detail SP">
                                    <i class="ti ti-eye text-sm"></i>
                                    <span class="hidden sm:inline">Detail</span>
                                </button>
                            @endcan

                            @can('pelanggaran.edit')
                                <!-- Edit -->
                                <button type="button" 
                                        class="btnEditPelanggaran inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs border border-emerald-200 shadow-2xs transition active:scale-95 cursor-pointer"
                                        data-nosp="{{ Crypt::encrypt($item->no_sp) }}"
                                        title="Edit Pelanggaran">
                                    <i class="ti ti-edit text-sm"></i>
                                    <span class="hidden sm:inline">Edit</span>
                                </button>
                            @endcan

                            @can('pelanggaran.delete')
                                <!-- Hapus -->
                                <form action="{{ route('pelanggaran.delete', Crypt::encrypt($item->no_sp)) }}" method="POST" class="inline-block formDeletePelanggaran">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center p-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs border border-rose-200 shadow-2xs transition active:scale-95 cursor-pointer"
                                            title="Hapus Surat Peringatan">
                                        <i class="ti ti-trash text-sm"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/90 shadow-2xs">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3.5 shadow-2xs">
                    <i class="ti ti-shield-check text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Data Pelanggaran</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Belum ada catatan surat peringatan atau sanksi kedisiplinan yang tersimpan untuk filter pencarian saat ini.
                </p>
                @can('pelanggaran.create')
                    <button type="button" id="btnEmptyCreatePelanggaran" 
                            class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition active:scale-95 cursor-pointer"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Data Baru</span>
                    </button>
                @endcan
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="pt-2">
            {{ $pelanggaran->links('v2.components.pagination') }}
        </div>
    </div>

</div>

<!-- ================= MODAL UNIVERSAL PELANGGARAN V2 ================= -->
<div id="modalPelanggaran" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Modal Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-file-alert text-lg" id="modalIcon"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="modalTitle">
                        Surat Peringatan & Pelanggaran
                    </h3>
                    <p class="text-[11px] text-slate-500" id="modalSubtitle">
                        Formulir data sanksi dan surat peringatan
                    </p>
                </div>
            </div>
            <button type="button" id="btnCloseModal" 
                    class="w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="modalContent">
            <!-- Content loaded via AJAX -->
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
    $(document).ready(function() {
        const theme1 = '{{ $themeColor1 }}';
        const theme2 = '{{ $themeColor2 }}';

        // 1. Inisialisasi Flatpickr pada Filter Bar
        if (typeof flatpickr !== 'undefined') {
            $('.flatpickr-filter').flatpickr({
                dateFormat: 'Y-m-d',
                allowInput: true
            });
        }

        // 2. Inisialisasi Select2 Filter jika ada
        if ($.fn.select2) {
            $('.select2NikFilter').select2({
                placeholder: 'Semua Karyawan',
                allowClear: true,
                width: '100%'
            });
        }

        // 3. Helper Modal
        const $modal = $('#modalPelanggaran');
        const $modalTitle = $('#modalTitle');
        const $modalSubtitle = $('#modalSubtitle');
        const $modalIcon = $('#modalIcon');
        const $modalContent = $('#modalContent');

        function openModal(title, subtitle, icon) {
            $modalTitle.text(title);
            $modalSubtitle.text(subtitle);
            $modalIcon.attr('class', 'ti ' + (icon || 'ti-file-alert') + ' text-lg');
            $modalContent.html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat data...</p>
                </div>
            `);
            $modal.removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeModal() {
            $modal.addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        $('#btnCloseModal').on('click', closeModal);
        $modal.on('click', function(e) {
            if (e.target === this) closeModal();
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$modal.hasClass('hidden')) {
                closeModal();
            }
        });

        // 4. Action: Tambah Pelanggaran (Create)
        $('#btnCreatePelanggaran, #btnEmptyCreatePelanggaran').on('click', function(e) {
            e.preventDefault();
            openModal('Tambah Surat Peringatan', 'Isi data formulir surat peringatan / sanksi kedisiplinan', 'ti-plus');

            $.ajax({
                url: "{{ route('pelanggaran.create') }}",
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
                    initFormPlugins();
                },
                error: function(xhr) {
                    $modalContent.html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat formulir.</p>
                            <p class="mt-1">${xhr.responseText || 'Terjadi kesalahan sistem.'}</p>
                        </div>
                    `);
                }
            });
        });

        // 5. Action: Detail Pelanggaran (Show)
        $(document).on('click', '.btnShowPelanggaran', function(e) {
            e.preventDefault();
            const noSp = $(this).data('nosp');
            openModal('Detail Surat Peringatan', 'Rincian dokumen resmi dan poin pelanggaran', 'ti-file-description');

            $.ajax({
                url: `{{ url('/pelanggaran') }}/${noSp}/show`,
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
                },
                error: function(xhr) {
                    $modalContent.html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat detail surat peringatan.</p>
                            <p class="mt-1">${xhr.responseText || 'Data tidak ditemukan.'}</p>
                        </div>
                    `);
                }
            });
        });

        // 6. Action: Edit Pelanggaran
        $(document).on('click', '.btnEditPelanggaran', function(e) {
            e.preventDefault();
            const noSp = $(this).data('nosp');
            openModal('Edit Surat Peringatan', 'Perbarui data sanksi dan butir pelanggaran', 'ti-edit');

            $.ajax({
                url: `{{ url('/pelanggaran') }}/${noSp}/edit`,
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
                    initFormPlugins();
                },
                error: function(xhr) {
                    $modalContent.html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat form edit.</p>
                            <p class="mt-1">${xhr.responseText || 'Data tidak ditemukan.'}</p>
                        </div>
                    `);
                }
            });
        });

        // 7. Action: Delete Pelanggaran (SweetAlert2)
        $(document).on('submit', '.formDeletePelanggaran', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Surat Peringatan?',
                text: 'Data pelanggaran dan catatan SP yang dihapus tidak dapat dipulihkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Data',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold text-xs px-4 py-2.5',
                    cancelButton: 'rounded-xl font-bold text-xs px-4 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // 8. Inisialisasi Plugin Form di Dalam Modal
        function initFormPlugins() {
            if (typeof flatpickr !== 'undefined') {
                $('.modal-flatpickr-date').flatpickr({
                    dateFormat: 'Y-m-d',
                    allowInput: false
                });
            }

            if ($.fn.select2) {
                $('.select2ModalNik').select2({
                    placeholder: 'Pilih Karyawan',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modalPelanggaran')
                });
            }
        }
    });
</script>
@endpush
