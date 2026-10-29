@extends('v2.layouts.app')

@section('titlepage', 'Penyesuaian Gaji')
@section('page_title', 'Master Penyesuaian Gaji')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $tahunDipilih = request('tahun', date('Y'));
    $totalPenyesuaian = $penyesuaiangaji->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTIONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-adjustments-horizontal text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Penyesuaian Gaji</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola periode penyesuaian upah bulanan (koreksi penambah & pemotong gaji karyawan)
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
                <span class="font-bold text-slate-800">Penyesuaian Gaji</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('penyesuaiangaji.create')
                    <!-- Tambah Penyesuaian Gaji -->
                    <button type="button" id="btnCreatePenyesuaian" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Periode Penyesuaian</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER BAR STANDAR V2 (FILTER TAHUN) ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('penyesuaiangaji.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-2.5 sm:gap-3 items-center">
                
                <!-- Filter Tahun -->
                <div class="md:col-span-4 relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-sm"></i>
                    </span>
                    <select name="tahun" id="filter_tahun" class="w-full pl-9 pr-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">-- Pilih Tahun --</option>
                        @for ($t = $start_year; $t <= date('Y'); $t++)
                            <option value="{{ $t }}" {{ $tahunDipilih == $t ? 'selected' : '' }}>
                                Tahun {{ $t }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Actions Button (Filter & Reset) -->
                <div class="md:col-span-3 flex items-center gap-1.5">
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Tampilkan</span>
                    </button>
                    @if (request()->has('tahun') && request('tahun') != date('Y'))
                        <a href="{{ route('penyesuaiangaji.index') }}" 
                           class="inline-flex items-center justify-center p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs cursor-pointer"
                           title="Reset Tahun Ini">
                            <i class="ti ti-refresh text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <!-- Divider with Counter -->
        <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="text-slate-500 text-[11.5px] flex items-center gap-2">
                <span>Menampilkan <strong>{{ $totalPenyesuaian }}</strong> Periode Penyesuaian Gaji Tahun <strong>{{ $tahunDipilih }}</strong></span>
            </div>
        </div>
    </div>

    <!-- ================= 3. TABEL DATA PENYESUAIAN GAJI V2 (COMPACT & 1 BARIS MURNI) ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        
        <!-- Table Top Banner Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-adjustments-horizontal"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Periode Penyesuaian Gaji</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                Tahun {{ $tahunDipilih }} ({{ $totalPenyesuaian }} periode)
            </span>
        </div>

        <!-- Responsive Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse">
                <!-- Table Header -->
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15 whitespace-nowrap"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 w-36 text-white/90 border-0 border-t-0">Kode Penyesuaian</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Periode Bulan</th>
                        <th class="py-2.5 px-4 text-center w-28 text-white/90 border-0 border-t-0">Tahun</th>
                        <th class="py-2.5 px-4 text-center w-40 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white whitespace-nowrap">
                    @forelse ($penyesuaiangaji as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- Nomor Baris -->
                            <td class="py-2.5 px-3 text-center text-slate-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <!-- Kode Penyesuaian Gaji -->
                            <td class="py-2.5 px-4">
                                <span class="inline-flex items-center font-mono font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200/80 text-xs">
                                    {{ $item->kode_penyesuaian_gaji }}
                                </span>
                            </td>

                            <!-- Periode Bulan -->
                            <td class="py-2.5 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/70 flex items-center justify-center text-xs font-bold shrink-0">
                                        <i class="ti ti-calendar-event text-sm"></i>
                                    </div>
                                    <span class="font-bold text-slate-900 text-xs sm:text-sm">
                                        {{ getNamabulan($item->bulan) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Tahun -->
                            <td class="py-2.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold text-slate-700 bg-slate-100 border border-slate-200">
                                    {{ $item->tahun }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-2.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                    @can('penyesuaiangaji.edit')
                                        <!-- Kelola / Set Karyawan -->
                                        <a href="{{ route('penyesuaiangaji.setkaryawan', Crypt::encrypt($item->kode_penyesuaian_gaji)) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold border border-emerald-200 text-xs transition cursor-pointer"
                                           title="Kelola Data Karyawan">
                                            <i class="ti ti-users text-sm"></i>
                                            <span>Set Karyawan</span>
                                        </a>

                                        <!-- Edit Periode -->
                                        <button type="button" 
                                                class="w-6.5 h-6.5 rounded-md hover:bg-white text-blue-600 hover:text-blue-700 flex items-center justify-center transition-all cursor-pointer btnEditPenyesuaian" 
                                                data-kodepenyesuaian="{{ Crypt::encrypt($item->kode_penyesuaian_gaji) }}"
                                                title="Edit Periode">
                                            <i class="ti ti-edit text-xs"></i>
                                        </button>
                                    @endcan

                                    @can('penyesuaiangaji.delete')
                                        <form action="{{ route('penyesuaiangaji.delete', Crypt::encrypt($item->kode_penyesuaian_gaji)) }}" method="POST" class="inline m-0 formDeletePenyesuaian">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-6.5 h-6.5 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition-all cursor-pointer" 
                                                    title="Hapus Periode">
                                                <i class="ti ti-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 px-4 text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-3">
                                    <i class="ti ti-calendar-off"></i>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800">Tidak ada data penyesuaian gaji di tahun {{ $tahunDipilih }}</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5 max-w-sm mx-auto">
                                    Belum ada periode penyesuaian gaji yang didaftarkan untuk tahun ini.
                                </p>
                                @can('penyesuaiangaji.create')
                                    <button type="button" id="btnEmptyCreatePenyesuaian" 
                                            class="mt-3 inline-flex items-center gap-1.5 px-3.5 py-1.5 text-white font-bold rounded-xl text-xs shadow-xs transition active:scale-95 cursor-pointer"
                                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                        <i class="ti ti-plus text-xs"></i>
                                        <span>Tambah Periode Baru</span>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= MODAL UNIVERSAL PENYESUAIAN GAJI V2 ================= -->
<div id="modalPenyesuaianGaji" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-adjustments-horizontal text-lg" id="modalIcon"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="modalTitle">
                        Penyesuaian Gaji
                    </h3>
                    <p class="text-[11px] text-slate-500" id="modalSubtitle">
                        Formulir periode penyesuaian gaji karyawan
                    </p>
                </div>
            </div>
            <button type="button" id="btnCloseModal" 
                    class="w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Body Content -->
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
    $(document).ready(function() {
        // 1. Modal Helper
        const $modal = $('#modalPenyesuaianGaji');
        const $modalTitle = $('#modalTitle');
        const $modalSubtitle = $('#modalSubtitle');
        const $modalIcon = $('#modalIcon');
        const $modalContent = $('#modalContent');

        function openModal(title, subtitle, icon) {
            $modalTitle.text(title);
            $modalSubtitle.text(subtitle);
            $modalIcon.attr('class', 'ti ' + (icon || 'ti-adjustments-horizontal') + ' text-lg');
            $modalContent.html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat formulir...</p>
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

        // 2. Tambah Periode Penyesuaian
        $('#btnCreatePenyesuaian, #btnEmptyCreatePenyesuaian').on('click', function(e) {
            e.preventDefault();
            openModal('Tambah Periode Penyesuaian', 'Tentukan bulan dan tahun penyesuaian gaji', 'ti-plus');

            $.ajax({
                url: "{{ route('penyesuaiangaji.create') }}",
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
                },
                error: function(xhr) {
                    $modalContent.html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat form tambah.</p>
                            <p class="mt-1">${xhr.responseText || 'Terjadi kesalahan.'}</p>
                        </div>
                    `);
                }
            });
        });

        // 3. Edit Periode Penyesuaian
        $(document).on('click', '.btnEditPenyesuaian', function(e) {
            e.preventDefault();
            const kodePenyesuaian = $(this).data('kodepenyesuaian');
            openModal('Edit Periode Penyesuaian', 'Perbarui bulan atau tahun penyesuaian gaji', 'ti-edit');

            $.ajax({
                url: `{{ url('/penyesuaiangaji') }}/${kodePenyesuaian}/edit`,
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
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

        // 4. Delete Single (SweetAlert2)
        $(document).on('submit', '.formDeletePenyesuaian', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Periode Penyesuaian?',
                text: 'Seluruh rincian penyesuaian gaji karyawan pada periode ini akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Periode',
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
    });
</script>
@endpush
