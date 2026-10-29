@extends('v2.layouts.app')

@section('titlepage', 'Periode Penilaian KPI')
@section('page_title', 'Periode Penilaian KPI')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalPeriods = $kpi_periods->total();
    $activePeriods = $active_periods_count ?? $kpi_periods->where('is_active', 1)->count();
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-calendar-stats text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Periode Penilaian KPI</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola siklus dan rentang waktu penilaian kinerja karyawan (KPI) secara terstruktur
            </p>
        </div>

        <!-- Breadcrumbs & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500">KPI</span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Periode</span>
            </nav>

            <!-- Actions Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="btnTambahPeriodeV2"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambah Periode Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER & STATS TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('kpi.periods.index') }}" method="GET">
            <div class="flex flex-col sm:flex-row items-center gap-2.5">
                
                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <input type="text" name="nama_periode" value="{{ request('nama_periode') }}" 
                           placeholder="Cari nama periode penilaian (misal: Semester 1, Januari 2026)..." 
                           class="w-full h-10 pl-9 pr-3.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <!-- Action Button Filter & Reset -->
                <div class="flex items-center gap-1.5 w-full sm:w-auto">
                    <button type="submit" 
                            class="flex-1 sm:flex-initial h-10 inline-flex items-center justify-center gap-1.5 px-4 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if (request()->has('nama_periode') && request('nama_periode') != '')
                        <a href="{{ route('kpi.periods.index') }}" 
                           class="h-10 w-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs cursor-pointer shrink-0"
                           title="Reset Filter">
                            <i class="ti ti-refresh text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Menampilkan <strong>{{ $kpi_periods->count() }}</strong> dari total <strong>{{ $kpi_periods->total() }}</strong> periode penilaian</span>
        </div>
    </div>

    <!-- ================= 3. LIST CARD FULL WIDTH ================= -->
    <div class="space-y-2.5">
        @forelse ($kpi_periods as $index => $item)
            @php
                $isActive = (bool) $item->is_active;
            @endphp
            <div class="bg-white rounded-2xl px-4 py-3.5 sm:px-5 sm:py-4 border border-slate-200/90 hover:border-emerald-300/80 shadow-2xs hover:shadow-xs transition-all duration-200 group">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3.5">
                    
                    <!-- Left: Icon & Nama Periode -->
                    <div class="flex items-center gap-3.5 flex-1 min-w-0">
                        <!-- Period Icon -->
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center shrink-0 border transition-all duration-200 {{ $isActive ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                            <i class="ti ti-calendar-stats text-xl"></i>
                        </div>

                        <!-- Name & Dates -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition truncate">
                                    {{ $item->nama_periode }}
                                </h3>

                                @if ($isActive)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs text-slate-500 mt-1">
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-calendar text-xs text-slate-400"></i>
                                    <span>Mulai: <strong class="text-slate-700 font-semibold">{{ date('d/m/Y', strtotime($item->start_date)) }}</strong></span>
                                </span>
                                <span>&bull;</span>
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-calendar-event text-xs text-slate-400"></i>
                                    <span>Selesai: <strong class="text-slate-700 font-semibold">{{ date('d/m/Y', strtotime($item->end_date)) }}</strong></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Duration badge & Action Buttons -->
                    <div class="flex items-center justify-between md:justify-end gap-3 shrink-0 pt-2.5 md:pt-0 border-t md:border-t-0 border-slate-100">
                        @php
                            $start = \Carbon\Carbon::parse($item->start_date);
                            $end = \Carbon\Carbon::parse($item->end_date);
                            $diffDays = $start->diffInDays($end) + 1;
                        @endphp
                        <div class="text-left md:text-right">
                            <span class="text-xs font-bold text-slate-800 block">
                                {{ $diffDays }} Hari
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium block">Durasi Siklus</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-1.5">
                            <!-- Edit Button -->
                            <button type="button" 
                                    class="btnEditPeriodeV2 p-2 rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200 transition active:scale-95 cursor-pointer shadow-2xs flex items-center gap-1 text-xs font-bold"
                                    data-id="{{ $item->id }}"
                                    title="Edit Periode">
                                <i class="ti ti-edit text-sm"></i>
                                <span class="hidden sm:inline text-[11px]">Edit</span>
                            </button>

                            <!-- Delete Button -->
                            <form action="{{ route('kpi.periods.delete', $item->id) }}" method="POST" class="deleteform m-0 inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btnDeletePeriodeV2 p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                        title="Hapus Periode">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 text-center text-slate-400 border border-slate-200/90 shadow-2xs">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                    <i class="ti ti-calendar-stats"></i>
                </div>
                <h5 class="text-sm font-bold text-slate-800">Belum Ada Periode Penilaian KPI</h5>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Data periode penilaian masih kosong. Buat periode baru untuk memulai siklus penilaian kinerja.
                </p>
                <button type="button" id="btnTambahPeriodeEmptyV2"
                        class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all active:scale-95 cursor-pointer"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambah Periode Baru</span>
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($kpi_periods->hasPages())
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
            {{ $kpi_periods->links('v2.components.pagination') }}
        </div>
    @endif

</div>

<!-- ================= UNIVERSAL MODAL TAMBAH / EDIT PERIODE KPI ================= -->
<div id="modalKpiPeriodV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300 flex flex-col my-auto">
        <!-- Header Modal -->
        <div class="px-5 sm:px-6 py-3.5 border-b border-slate-200/80 bg-white flex items-center justify-between relative shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-700 bg-slate-100 border border-slate-200">
                    <i class="ti ti-calendar-plus text-base"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base" id="modalKpiPeriodTitle">
                        Tambah Periode Penilaian
                    </h3>
                    <p class="text-[11px] text-slate-500">
                        Atur nama periode serta tanggal mulai dan selesai
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseModalKpiPeriod w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center border border-slate-200 transition cursor-pointer">
                <i class="ti ti-x text-sm"></i>
            </button>
        </div>

        <!-- Body Content Modal (Loaded via AJAX) -->
        <div class="p-5 sm:p-6 overflow-y-auto" id="modalKpiPeriodContent">
            <div class="py-10 text-center text-slate-400">
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
        function showModal(title, url, isPost = false, postData = {}) {
            $('#modalKpiPeriodTitle').text(title);
            $('#modalKpiPeriodV2').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            $('#modalKpiPeriodContent').html(`
                <div class="py-10 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat formulir...</p>
                </div>
            `);

            if (isPost) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: postData,
                    success: function(response) {
                        $('#modalKpiPeriodContent').html(response);
                    },
                    error: function() {
                        $('#modalKpiPeriodContent').html('<div class="text-rose-500 text-xs text-center py-4">Gagal memuat formulir.</div>');
                    }
                });
            } else {
                $('#modalKpiPeriodContent').load(url);
            }
        }

        function closeModal() {
            $('#modalKpiPeriodV2').addClass('hidden');
            $('body').removeClass('overflow-hidden');
            $('#modalKpiPeriodContent').html('');
        }

        // Open Create Modal
        $('#btnTambahPeriodeV2, #btnTambahPeriodeEmptyV2').click(function(e) {
            e.preventDefault();
            showModal('Tambah Periode Penilaian', '{{ route("kpi.periods.create") }}');
        });

        // Open Edit Modal
        $(document).on('click', '.btnEditPeriodeV2', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            showModal('Edit Periode Penilaian', '{{ route("kpi.periods.edit") }}', true, {
                _token: '{{ csrf_token() }}',
                id: id
            });
        });

        $(document).on('click', '.btnCloseModalKpiPeriod', closeModal);

        $('#modalKpiPeriodV2').click(function(e) {
            if (e.target === this) closeModal();
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$('#modalKpiPeriodV2').hasClass('hidden')) {
                closeModal();
            }
        });

        // Delete Confirmation
        $(document).on('click', '.btnDeletePeriodeV2', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Periode KPI?',
                text: 'Periode penilaian yang dihapus tidak dapat dipulihkan!',
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
